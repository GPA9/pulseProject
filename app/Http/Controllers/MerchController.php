<?php

namespace App\Http\Controllers;

use App\Models\Merch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MerchController extends Controller
{
    public function index(Request $request)
    {
        $query = Merch::with('musicianProfile');

        // Search by group/band name
        if ($search = $request->input('search')) {
            $query->whereHas('musicianProfile', function ($q) use ($search) {
                $q->where('stage_name', 'like', "%{$search}%");
            })->orWhere('name', 'like', "%{$search}%");
        }

        // Filter by community
        if ($community = $request->input('community')) {
            $query->whereHas('musicianProfile', function ($q) use ($community) {
                $q->where('autonomous_community', $community);
            });
        }

        // Filter by province
        if ($province = $request->input('province')) {
            $query->whereHas('musicianProfile', function ($q) use ($province) {
                $q->where('province', $province);
            })->orWhere('city', 'like', "%{$province}%");
        }

        // Filter by category
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // Sorting
        $sort = $request->input('sort', '');
        match ($sort) {
            'cheapest' => $query->orderBy('price', 'asc'),
            'expensive' => $query->orderBy('price', 'desc'),
            'bestsellers' => $query->orderBy('sales_count', 'desc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            default => $query->orderBy('id', 'asc'),
        };

        $merches = $query->get();
        $categories = Merch::distinct()->pluck('category')->sort()->values();
        $communities = ConcertController::getCommunitiesMap();

        return view('merch.index', compact('merches', 'categories', 'communities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'sizes' => 'nullable|string|max:255',
            'merchbar_url' => [
                'required',
                'url',
                'max:500',
                'regex:/^https?:\/\/([a-z0-9-]+\.)*merchbar\.[a-z.]+/i',
            ],
            'image' => 'nullable|image|max:4096',
        ]);

        $profile = Auth::user()->musicianProfile;
        if (!$profile) {
            return back()->with('error', 'Necesitas un perfil de artista para añadir merchandising.');
        }

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('merch', 'public');
        }

        Merch::create([
            'musician_profile_id' => $profile->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'city' => $request->city ?? $profile->city,
            'image_path' => $path,
            'sizes' => $request->filled('sizes')
                ? array_values(array_filter(array_map('trim', explode(',', $request->sizes))))
                : null,
            'merchbar_url' => $request->merchbar_url,
            'sales_count' => 0,
        ]);

        return redirect()->route('dashboard')->with('success', '¡Producto de merchandising añadido correctamente!');
    }

    /**
     * Show form for editing the specified merch.
     */
    public function edit($id)
    {
        $merch = Merch::findOrFail($id);
        
        // Verify ownership
        if ($merch->musicianProfile->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar este producto.');
        }
        
        return view('merch.edit', compact('merch'));
    }

    /**
     * Update the specified merch.
     */
    public function update(Request $request, $id)
    {
        $merch = Merch::findOrFail($id);
        
        // Verify ownership
        if ($merch->musicianProfile->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar este producto.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'sizes' => 'nullable|string|max:255',
            'merchbar_url' => [
                'required',
                'url',
                'max:500',
                'regex:/^https?:\/\/([a-z0-9-]+\.)*merchbar\.[a-z.]+/i',
            ],
            'image' => 'nullable|image|max:4096',
        ]);

        $path = $merch->image_path;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('merch', 'public');
        }

        $merch->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'city' => $request->city ?? $merch->musicianProfile->city,
            'image_path' => $path,
            'sizes' => $request->filled('sizes')
                ? array_values(array_filter(array_map('trim', explode(',', $request->sizes))))
                : null,
            'merchbar_url' => $request->merchbar_url,
        ]);

        return redirect()->route('dashboard')->with('success', '¡Producto de merchandising actualizado correctamente!');
    }

    /**
     * Remove the specified merch.
     */
    public function destroy($id)
    {
        $merch = Merch::findOrFail($id);
        
        // Verify ownership
        if ($merch->musicianProfile->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para eliminar este producto.');
        }

        $merch->delete();

        return redirect()->route('dashboard')->with('success', '¡Producto de merchandising eliminado correctamente!');
    }
}
