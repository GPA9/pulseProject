<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SongController extends Controller
{
    /**
     * Store a single song (optionally attached to an existing album)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'song_file' => 'required_without:audio_file|file|mimes:mp3,wav,flac|max:25600',
            'audio_file' => 'required_without:song_file|file|mimes:mp3,wav,flac|max:25600',
            'cover_image' => 'nullable|image|max:2048',
            'album_id' => 'nullable|exists:albums,id',
        ]);

        $musician = Auth::user()->musicianProfile;
        if (!$musician) {
            return redirect()->back()->with('error', 'Debes crear un perfil de músico primero.');
        }

        // Check active subscription
        if (!$musician->subscriptions()->active()->exists()) {
            return redirect()->back()->with('error', 'Necesitas un plan de almacenamiento activo para subir música.');
        }

        // If album_id provided, verify it belongs to this musician
        $albumId = null;
        if ($request->album_id) {
            $album = Album::where('id', $request->album_id)
                ->where('musician_profile_id', $musician->id)
                ->first();
            $albumId = $album?->id;
        }

        $audioFile = $request->file('song_file') ?? $request->file('audio_file');
        $songPath = $audioFile->store('songs', 'public');
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        Song::create([
            'musician_profile_id' => $musician->id,
            'album_id' => $albumId,
            'title' => $request->title,
            'file_path' => $songPath,
            'cover_path' => $coverPath,
        ]);

        return redirect()->back()->with('success', '¡Pista subida con éxito!');
    }

    /**
     * Create a new album and optionally upload songs to it
     */
    public function storeAlbum(Request $request)
    {
        $usesFlatDashboardFormat = $request->has('song_titles') || $request->hasFile('song_files');

        if ($usesFlatDashboardFormat) {
            $request->validate([
                'title' => 'required|string|max:255',
                'album_cover' => 'nullable|image|max:4096',
                'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
                'song_titles' => 'required|array|min:1',
                'song_titles.*' => 'required|string|max:255',
                'song_files' => 'required|array|min:1',
                'song_files.*' => 'required|file|mimes:mp3,wav,flac|max:25600',
            ]);
        } else {
            $request->validate([
                'album_title' => 'required|string|max:255',
                'album_cover' => 'nullable|image|max:4096',
                'album_description' => 'nullable|string|max:1000',
                'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
                'songs' => 'required|array|min:1',
                'songs.*.title' => 'required|string|max:255',
                'songs.*.file' => 'required|file|mimes:mp3,wav,flac|max:25600',
                'songs.*.cover' => 'nullable|image|max:2048',
            ]);
        }

        $musician = Auth::user()->musicianProfile;
        if (!$musician) {
            return redirect()->back()->with('error', 'Debes crear un perfil de músico primero.');
        }

        // Check active subscription
        if (!$musician->subscriptions()->active()->exists()) {
            return redirect()->back()->with('error', 'Necesitas un plan de almacenamiento activo para subir música.');
        }

        // Album cover
        $albumCoverPath = null;
        if ($request->hasFile('album_cover')) {
            $albumCoverPath = $request->file('album_cover')->store('covers', 'public');
        }

        // Create album
        $albumTitle = $request->input('album_title', $request->input('title'));
        $releaseYear = $request->input('release_year', $request->input('year'));
        $albumDescription = $request->input('album_description');

        $album = Album::create([
            'musician_profile_id' => $musician->id,
            'title' => $albumTitle,
            'cover_path' => $albumCoverPath,
            'description' => $albumDescription,
            'release_year' => $releaseYear,
        ]);

        // Upload each song in the album
        if ($usesFlatDashboardFormat) {
            $titles = $request->input('song_titles', []);
            $files = $request->file('song_files', []);

            foreach ($titles as $index => $songTitle) {
                if (!isset($files[$index])) {
                    continue;
                }

                $songPath = $files[$index]->store('songs', 'public');

                Song::create([
                    'musician_profile_id' => $musician->id,
                    'album_id' => $album->id,
                    'title' => $songTitle,
                    'file_path' => $songPath,
                    'cover_path' => null,
                ]);
            }
        } else {
            foreach ($request->songs as $songData) {
                $songPath = $songData['file']->store('songs', 'public');
                $coverPath = null;
                if (isset($songData['cover']) && $songData['cover']) {
                    $coverPath = $songData['cover']->store('covers', 'public');
                }

                Song::create([
                    'musician_profile_id' => $musician->id,
                    'album_id' => $album->id,
                    'title' => $songData['title'],
                    'file_path' => $songPath,
                    'cover_path' => $coverPath,
                ]);
            }
        }

        return redirect()->back()->with('success', "¡Álbum \"{$album->title}\" creado con {$album->songs->count()} pistas!");
    }

    /**
     * Delete a song owned by the authenticated musician
     */
    public function destroy(Song $song)
    {
        $musician = Auth::user()->musicianProfile;
        if (!$musician || $song->musician_profile_id !== $musician->id) {
            abort(403, 'No tienes permiso para eliminar esta canción.');
        }

        if (!empty($song->file_path) && Storage::disk('public')->exists($song->file_path)) {
            Storage::disk('public')->delete($song->file_path);
        }

        if (!empty($song->cover_path) && Storage::disk('public')->exists($song->cover_path)) {
            Storage::disk('public')->delete($song->cover_path);
        }

        $song->delete();

        return redirect()->back()->with('success', 'Canción eliminada correctamente.');
    }

}
