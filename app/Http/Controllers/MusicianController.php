<?php

namespace App\Http\Controllers;

use App\Models\MusicianProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MusicianController extends Controller
{
    public function index(Request $request)
    {
        $communities = ConcertController::getCommunitiesMap();

        $query = MusicianProfile::with('user');

        // Búsqueda por nombre, género o ciudad
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('stage_name', 'like', "%{$search}%")
                  ->orWhere('genre', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('autonomous_community', 'like', "%{$search}%");
            });
        }

        if ($community = $request->input('community')) {
            $query->where('autonomous_community', $community);
        }

        if ($province = $request->input('province')) {
            $query->where('province', $province);
        }

        $musicians = $query->get();

        return view('musicians.index', compact('musicians', 'communities'));
    }

    public function show(MusicianProfile $musician)
    {
        $musician->load(['songs', 'concerts', 'merch', 'albums.songs']);
        return view('musicians.show', compact('musician'));
    }

    public function create()
    {
        $communities = ConcertController::getCommunitiesMap();
        return view('musicians.create', compact('communities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'stage_name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'city' => 'required|string|max:255',
            'province' => 'nullable|string|max:255',
            'autonomous_community' => 'nullable|string|max:255',
            'genre' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('musicians', 'public');
        }

        MusicianProfile::create([
            'user_id' => Auth::id(),
            'stage_name' => $request->stage_name,
            'bio' => $request->bio,
            'city' => $request->city,
            'province' => $request->province,
            'autonomous_community' => $request->autonomous_community,
            'genre' => $request->genre,
            'image_path' => $path,
        ]);

        $user = Auth::user();
        $user->role = 'musician';
        $user->save();

        return redirect()->route('dashboard')->with('success', '¡Perfil de artista creado correctamente!');
    }

    public function edit(MusicianProfile $musician)
    {
        if ($musician->user_id !== Auth::id()) {
            abort(403);
        }
        $communities = ConcertController::getCommunitiesMap();
        return view('musicians.edit', compact('musician', 'communities'));
    }

    public function update(Request $request, MusicianProfile $musician)
    {
        if ($musician->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'stage_name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'city' => 'required|string|max:255',
            'province' => 'nullable|string|max:255',
            'autonomous_community' => 'nullable|string|max:255',
            'genre' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('musicians', 'public');
            $musician->image_path = $path;
        }

        $musician->update([
            'stage_name' => $request->stage_name,
            'bio' => $request->bio,
            'city' => $request->city,
            'province' => $request->province,
            'autonomous_community' => $request->autonomous_community,
            'genre' => $request->genre,
        ]);

        return redirect()->route('dashboard')->with('success', '¡Perfil actualizado correctamente!');
    }
}
