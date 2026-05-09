<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $musicianProfile = $user->musicianProfile;
        
        return view('profile.edit', compact('user', 'musicianProfile'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Update basic user info
        $user->fill($request->validated());
        $user->save();

        // Update musician profile if exists
        if ($user->musicianProfile) {
            $musicianData = $request->validate([
                'stage_name' => ['nullable', 'string', 'max:255'],
                'genre' => ['nullable', 'string', 'max:100'],
                'city' => ['nullable', 'string', 'max:100'],
                'province' => ['nullable', 'string', 'max:100'],
                'autonomous_community' => ['nullable', 'string', 'max:100'],
                'bio' => ['nullable', 'string', 'max:1000'],
            ]);

            // Build social networks JSON
            $socialNetworks = array_filter([
                'instagram' => $request->input('social_instagram'),
                'twitter' => $request->input('social_twitter'),
                'facebook' => $request->input('social_facebook'),
                'youtube' => $request->input('social_youtube'),
                'tiktok' => $request->input('social_tiktok'),
            ], fn($v) => $v !== null && $v !== '');

            // Build streaming platforms JSON
            $streamingPlatforms = array_filter([
                'spotify' => $request->input('platform_spotify'),
                'apple' => $request->input('platform_apple'),
                'soundcloud' => $request->input('platform_soundcloud'),
                'deezer' => $request->input('platform_deezer'),
            ], fn($v) => $v !== null && $v !== '');

            $musicianData['social_networks'] = !empty($socialNetworks) ? $socialNetworks : null;
            $musicianData['streaming_platforms'] = !empty($streamingPlatforms) ? $streamingPlatforms : null;

            $user->musicianProfile->update(array_filter($musicianData, fn($v) => $v !== null));
        }

        return Redirect::route('dashboard')->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Update profile image.
     */
    public function updateImage(Request $request)
    {
        $request->validate([
            'profile_image' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:5120'], // 5MB max
        ]);

        $user = Auth::user();
        $musicianProfile = $user->musicianProfile;

        if (!$musicianProfile) {
            return back()->withErrors(['profile_image' => 'No tienes un perfil de músico asociado.']);
        }

        // Delete old image if exists
        if ($musicianProfile->image_path) {
            $oldImagePath = public_path('images/band-logos/' . $musicianProfile->image_path);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Upload new image
        $image = $request->file('profile_image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('images/band-logos'), $imageName);

        $musicianProfile->update(['image_path' => $imageName]);

        return redirect()->route('dashboard')->with('success', 'Foto de perfil actualizada correctamente.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
