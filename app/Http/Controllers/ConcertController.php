<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConcertController extends Controller
{
    // Mapa de comunidades autónomas y sus provincias
    public static function getCommunitiesMap(): array
    {
        return [
            'Andalucía' => ['Almería', 'Cádiz', 'Córdoba', 'Granada', 'Huelva', 'Jaén', 'Málaga', 'Sevilla'],
            'Aragón' => ['Huesca', 'Teruel', 'Zaragoza'],
            'Asturias' => ['Asturias'],
            'Baleares' => ['Baleares'],
            'Canarias' => ['Las Palmas', 'Santa Cruz de Tenerife'],
            'Cantabria' => ['Cantabria'],
            'Castilla-La Mancha' => ['Albacete', 'Ciudad Real', 'Cuenca', 'Guadalajara', 'Toledo'],
            'Castilla y León' => ['Ávila', 'Burgos', 'León', 'Palencia', 'Salamanca', 'Segovia', 'Soria', 'Valladolid', 'Zamora'],
            'Cataluña' => ['Barcelona', 'Girona', 'Lleida', 'Tarragona'],
            'Extremadura' => ['Badajoz', 'Cáceres'],
            'Galicia' => ['A Coruña', 'Lugo', 'Ourense', 'Pontevedra'],
            'La Rioja' => ['La Rioja'],
            'Madrid' => ['Madrid'],
            'Murcia' => ['Murcia'],
            'Navarra' => ['Navarra'],
            'País Vasco' => ['Álava', 'Guipúzcoa', 'Vizcaya'],
            'Valencia' => ['Alicante', 'Castellón', 'Valencia'],
            'Ceuta' => ['Ceuta'],
            'Melilla' => ['Melilla'],
        ];
    }

    public function index(Request $request)
    {
        $query = Concert::with('musicianProfile')
            ->where('date', '>=', now())
            ->orderBy('date', 'asc');

        // Búsqueda por nombre de artista/grupo
        if ($search = $request->input('search')) {
            $query->whereHas('musicianProfile', function ($q) use ($search) {
                $q->where('stage_name', 'like', "%{$search}%");
            })->orWhere('venue', 'like', "%{$search}%");
        }

        // Filtro por comunidad autónoma
        if ($community = $request->input('community')) {
            $query->where('autonomous_community', $community);
        }

        // Filtro por provincia
        if ($province = $request->input('province')) {
            $query->where('province', $province);
        }

        // Filtro por género musical
        if ($genre = $request->input('genre')) {
            $query->where('genre', $genre);
        }

        // Filtro por precio máximo
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        // Filtro por cercanía (coordenadas del usuario)
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $radius = $request->input('radius', 100); // km por defecto

        // Ordenación
        $sort = $request->input('sort', 'date_asc');
        match ($sort) {
            'date_asc' => $query->reorder('date', 'asc'),
            'date_desc' => $query->reorder('date', 'desc'),
            'price_asc' => $query->reorder('price', 'asc'),
            'price_desc' => $query->reorder('price', 'desc'),
            'nearby' => $query->reorder('date', 'asc'), // Se ordenará por distancia después
            default => $query->reorder('date', 'asc'),
        };

        $concerts = $query->get();

        // Si hay coordenadas, filtrar por distancia
        if ($lat && $lng) {
            $concerts = $concerts->map(function ($concert) use ($lat, $lng) {
                $concert->distance = $concert->getDistanceFrom((float)$lat, (float)$lng);
                return $concert;
            })->filter(fn($c) => $c->distance !== null && $c->distance <= $radius)
              ->sortBy('distance')
              ->values();
        }

        $genres = Concert::where('date', '>=', now())->whereNotNull('genre')->distinct()->pluck('genre')->sort()->values();
        $communities = self::getCommunitiesMap();

        return view('concerts.index', compact('concerts', 'genres', 'communities'));
    }

    /**
     * Get concerts with coordinates as JSON for Leaflet map
     */
    public function mapData(Request $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $radius = $request->input('radius', 100);

        $concerts = Concert::with('musicianProfile')
            ->where('date', '>=', now())
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        if ($lat && $lng) {
            $concerts = $concerts->map(function ($concert) use ($lat, $lng) {
                $concert->distance = $concert->getDistanceFrom((float)$lat, (float)$lng);
                return $concert;
            })->filter(fn($c) => $c->distance !== null && $c->distance <= $radius)
              ->sortBy('distance')
              ->values();
        }

        return response()->json([
            'concerts' => $concerts->map(fn($c) => [
                'id' => $c->id,
                'artist' => $c->musicianProfile->stage_name ?? 'Artista',
                'venue' => $c->venue,
                'city' => $c->city,
                'date' => $c->date->format('d/m/Y H:i'),
                'price' => $c->price,
                'lat' => $c->latitude,
                'lng' => $c->longitude,
                'distance' => $c->distance ?? null,
            ])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'autonomous_community' => 'required|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'date' => 'required|date|after:now',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'capacity' => 'nullable|integer|min:1',
            'genre' => 'nullable|string|max:255',
            'ticketmaster_url' => [
                'required',
                'url',
                'max:500',
                'regex:/^https?:\/\/([a-z0-9-]+\.)*ticketmaster\.[a-z.]+/i',
            ],
        ]);

        $profile = \Auth::user()->musicianProfile;
        if (!$profile) {
            return back()->with('error', 'Necesitas un perfil de artista para añadir conciertos.');
        }

        // Geocodificar la dirección para obtener coordenadas
        $geocoded = $this->geocodeAddress($request->address);
        
        if (!$geocoded) {
            return back()->with('error', 'No se pudo encontrar la dirección. Por favor, verifica que sea correcta.');
        }

        $profile->concerts()->create([
            'autonomous_community' => $request->autonomous_community,
            'province' => $request->province,
            'city' => $request->city,
            'venue' => $request->venue,
            'address' => $request->address,
            'date' => $request->date,
            'price' => $request->price,
            'description' => $request->description,
            'capacity' => $request->capacity,
            'genre' => $request->genre,
            'ticketmaster_url' => $request->ticketmaster_url,
            'latitude' => $geocoded['lat'],
            'longitude' => $geocoded['lng'],
        ]);

        return redirect()->route('dashboard')->with('success', '¡Concierto añadido correctamente!');
    }

    /**
     * Geocodificar una dirección usando Nominatim API (OSM)
     */
    private function geocodeAddress(string $address): ?array
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get('https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                    'countrycodes' => 'es', // Limitar a España
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                return [
                    'lat' => (float)$data[0]['lat'],
                    'lng' => (float)$data[0]['lon'],
                ];
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Geocoding error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Show the form for editing the specified concert.
     */
    public function edit($id)
    {
        $concert = Concert::findOrFail($id);
        
        // Verify ownership
        if ($concert->musicianProfile->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar este concierto.');
        }
        
        return view('concerts.edit', compact('concert'));
    }

    /**
     * Update the specified concert.
     */
    public function update(Request $request, $id)
    {
        $concert = Concert::findOrFail($id);
        
        // Verify ownership
        if ($concert->musicianProfile->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar este concierto.');
        }

        $request->validate([
            'autonomous_community' => 'required|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'date' => 'required|date|after:now',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'capacity' => 'nullable|integer|min:1',
            'genre' => 'nullable|string|max:255',
            'ticketmaster_url' => [
                'required',
                'url',
                'max:500',
                'regex:/^https?:\/\/([a-z0-9-]+\.)*ticketmaster\.[a-z.]+/i',
            ],
        ]);

        // Geocodificar la dirección para obtener coordenadas
        $geocoded = $this->geocodeAddress($request->address);
        
        if (!$geocoded) {
            return back()->with('error', 'No se pudo encontrar la dirección. Por favor, verifica que sea correcta.');
        }

        $concert->update([
            'autonomous_community' => $request->autonomous_community,
            'province' => $request->province,
            'city' => $request->city,
            'venue' => $request->venue,
            'address' => $request->address,
            'date' => $request->date,
            'price' => $request->price,
            'description' => $request->description,
            'capacity' => $request->capacity,
            'genre' => $request->genre,
            'ticketmaster_url' => $request->ticketmaster_url,
            'latitude' => $geocoded['lat'],
            'longitude' => $geocoded['lng'],
        ]);

        return redirect()->route('dashboard')->with('success', '¡Concierto actualizado correctamente!');
    }

    /**
     * Remove the specified concert.
     */
    public function destroy($id)
    {
        $concert = Concert::findOrFail($id);
        
        // Verify ownership
        if ($concert->musicianProfile->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para eliminar este concierto.');
        }

        $concert->delete();

        return redirect()->route('dashboard')->with('success', '¡Concierto eliminado correctamente!');
    }
}
