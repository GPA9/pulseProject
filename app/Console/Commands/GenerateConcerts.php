<?php

namespace App\Console\Commands;

use App\Models\Concert;
use App\Models\MusicianProfile;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateConcerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'concerts:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate concerts for existing musicians';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎤 Generando conciertos para artistas...');

        // Venues españolas reales
        $venues = [
            'Sala Barts', 'Sala Apolo', 'Razzmatazz', 'La2', 'Bikini',
            'Teatro Lara', 'Auditorio Nacional', 'La Riviera', 'Café Berlín',
            'Salle BBK', 'Sala Príncipe Pío', 'Moby Dick', 'Pabellón 3',
            'Teatro Alcázar', 'La Fontana d\'Or', 'El Palau', 'Tarantula',
        ];

        $musicians = MusicianProfile::all();
        $generated = 0;

        foreach ($musicians as $musician) {
            // 1-3 conciertos por artista
            $concertCount = rand(1, 3);

            for ($i = 0; $i < $concertCount; $i++) {
                // Fecha aleatoria entre 1-90 días desde hoy
                $date = Carbon::now()->addDays(rand(1, 90))->setTime(rand(19, 23), rand(0, 59));

                // Seleccionar venue aleatoria
                $venue = $venues[array_rand($venues)];

                Concert::create([
                    'musician_profile_id' => $musician->id,
                    'venue' => $venue,
                    'city' => $musician->city,
                    'province' => $musician->province ?? 'Unknown',
                    'autonomous_community' => $musician->autonomous_community,
                    'date' => $date,
                    'price' => rand(15, 50),
                    'description' => "{$musician->stage_name} en concierto en {$venue}. {$musician->genre} en vivo.",
                    'capacity' => rand(200, 1000),
                    'capacity_available' => rand(50, 200),
                    'genre' => $musician->genre,
                    'latitude' => $this->getCityLatitude($musician->city),
                    'longitude' => $this->getCityLongitude($musician->city),
                ]);

                $generated++;
            }
        }

        $this->info("✅ Conciertos generados: $generated");
        $this->info("🎵 Total de conciertos: " . Concert::count());
    }

    /**
     * Get latitude for Spanish cities
     */
    private function getCityLatitude($city)
    {
        $coords = [
            'Barcelona' => 41.3874,
            'Madrid' => 40.4168,
            'Valencia' => 39.4699,
            'Sevilla' => 37.3886,
            'Bilbao' => 43.2630,
            'Málaga' => 36.7196,
            'Alicante' => 38.3452,
            'Córdoba' => 37.8882,
            'Valladolid' => 41.6523,
            'Zaragoza' => 41.6560,
            'Gijón' => 43.5385,
            'Salamanca' => 40.9701,
            'Murcia' => 37.9922,
            'Palma' => 39.5696,
            'Toledo' => 39.8581,
            'Vitoria' => 42.8453,
            'Girona' => 41.9900,
            'Santiago de Compostela' => 42.8805,
        ];

        return $coords[$city] ?? 40.4168; // Default Madrid
    }

    /**
     * Get longitude for Spanish cities
     */
    private function getCityLongitude($city)
    {
        $coords = [
            'Barcelona' => 2.1686,
            'Madrid' => -3.7038,
            'Valencia' => -0.3763,
            'Sevilla' => -5.9864,
            'Bilbao' => -2.9244,
            'Málaga' => -4.4212,
            'Alicante' => -0.4816,
            'Córdoba' => -4.7794,
            'Valladolid' => -4.7245,
            'Zaragoza' => -0.8773,
            'Gijón' => -3.6352,
            'Salamanca' => -5.6639,
            'Murcia' => -1.1302,
            'Palma' => 2.6502,
            'Toledo' => -4.0226,
            'Vitoria' => -2.6734,
            'Girona' => 2.8235,
            'Santiago de Compostela' => -8.5545,
        ];

        return $coords[$city] ?? -3.7038; // Default Madrid
    }
}
