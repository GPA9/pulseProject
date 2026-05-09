<?php

namespace Database\Seeders;

use App\Models\Concert;
use App\Models\MusicianProfile;
use Illuminate\Database\Seeder;

class ConcertSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar conciertos existentes
        Concert::truncate();

        $musicians = MusicianProfile::all()->keyBy('stage_name');

        // Artistas del seeder original
        $m1 = $musicians->get('Luna Barcelona');
        $m2 = $musicians->get('Trueno Madrid');
        $m3 = $musicians->get('Brisa Valenciana');

        $concerts = [
            // ─── Luna Barcelona ────────────────────────────────────────
            [
                'musician_profile_id' => $m1?->id ?? 1,
                'venue' => 'Sala Apolo',
                'city' => 'Barcelona',
                'province' => 'Barcelona',
                'autonomous_community' => 'Cataluña',
                'date' => now()->addDays(10)->setTime(21, 0),
                'price' => 15.00,
                'description' => 'Una noche mágica de indie pop en el emblemático Sala Apolo. Luna Barcelona presenta su nuevo álbum "Sueños de Verano" en directo.',
                'capacity' => 900,
                'genre' => 'Indie Pop',
            ],
            [
                'musician_profile_id' => $m1?->id ?? 1,
                'venue' => 'Razzmatazz',
                'city' => 'Barcelona',
                'province' => 'Barcelona',
                'autonomous_community' => 'Cataluña',
                'date' => now()->addDays(20)->setTime(22, 0),
                'price' => 18.50,
                'description' => 'Razzmatazz acoge la gira de presentación del single "Noche en Gràcia". Efectos visuales y pantalla LED gigante.',
                'capacity' => 1500,
                'genre' => 'Indie Pop',
            ],
            [
                'musician_profile_id' => $m1?->id ?? 1,
                'venue' => 'Teatre Grec',
                'city' => 'Barcelona',
                'province' => 'Barcelona',
                'autonomous_community' => 'Cataluña',
                'date' => now()->addDays(45)->setTime(21, 30),
                'price' => 25.00,
                'description' => 'Noche especial en el Teatre Grec, al aire libre con las mejores vistas de Barcelona.',
                'capacity' => 1900,
                'genre' => 'Indie Pop',
            ],
            // ─── Trueno Madrid ─────────────────────────────────────────
            [
                'musician_profile_id' => $m2?->id ?? 2,
                'venue' => 'La Riviera',
                'city' => 'Madrid',
                'province' => 'Madrid',
                'autonomous_community' => 'Madrid',
                'date' => now()->addDays(5)->setTime(21, 0),
                'price' => 20.00,
                'description' => 'Trueno Madrid en uno de los recintos más icónicos de la capital. Rock alternativo a máximo volumen.',
                'capacity' => 1800,
                'genre' => 'Rock Alternativo',
            ],
            [
                'musician_profile_id' => $m2?->id ?? 2,
                'venue' => 'WiZink Center',
                'city' => 'Madrid',
                'province' => 'Madrid',
                'autonomous_community' => 'Madrid',
                'date' => now()->addDays(35)->setTime(20, 0),
                'price' => 35.00,
                'description' => 'El gran concierto del año. Trueno Madrid llena el WiZink Center con su gira "Malasaña Hardcore Tour".',
                'capacity' => 18000,
                'genre' => 'Rock Alternativo',
            ],
            [
                'musician_profile_id' => $m2?->id ?? 2,
                'venue' => 'Sala El Sol',
                'city' => 'Madrid',
                'province' => 'Madrid',
                'autonomous_community' => 'Madrid',
                'date' => now()->addDays(60)->setTime(22, 0),
                'price' => 12.00,
                'description' => 'Sesión íntima en Sala El Sol. Aforo limitado para vivir el rock de cerca.',
                'capacity' => 300,
                'genre' => 'Rock Alternativo',
            ],
            // ─── Brisa Valenciana ──────────────────────────────────────
            [
                'musician_profile_id' => $m3?->id ?? 3,
                'venue' => 'Palau de la Música',
                'city' => 'Valencia',
                'province' => 'Valencia',
                'autonomous_community' => 'Valencia',
                'date' => now()->addDays(15)->setTime(20, 0),
                'price' => 22.00,
                'description' => 'Jazz Fusión mediterráneo en el majestuoso Palau de la Música. Una velada única con el mar como telón de fondo.',
                'capacity' => 1800,
                'genre' => 'Jazz Fusión',
            ],
            [
                'musician_profile_id' => $m3?->id ?? 3,
                'venue' => 'La Marina de Valencia',
                'city' => 'Valencia',
                'province' => 'Valencia',
                'autonomous_community' => 'Valencia',
                'date' => now()->addDays(40)->setTime(21, 0),
                'price' => 18.00,
                'description' => 'Concierto al aire libre en La Marina. Brisa Valenciana presenta "Mar y Fuego" bajo las estrellas.',
                'capacity' => 3000,
                'genre' => 'Jazz Fusión',
            ],
            // ─── Conciertos extra en otras ciudades ────────────────────
            [
                'musician_profile_id' => $m2?->id ?? 2,
                'venue' => 'Auditorio Feria de Sevilla',
                'city' => 'Sevilla',
                'province' => 'Sevilla',
                'autonomous_community' => 'Andalucía',
                'date' => now()->addDays(25)->setTime(21, 30),
                'price' => 22.00,
                'description' => 'Gira andaluza de Trueno Madrid. Rock alternativo en el corazón de Sevilla.',
                'capacity' => 5000,
                'genre' => 'Rock Alternativo',
            ],
            [
                'musician_profile_id' => $m1?->id ?? 1,
                'venue' => 'Teatro Cervantes',
                'city' => 'Málaga',
                'province' => 'Málaga',
                'autonomous_community' => 'Andalucía',
                'date' => now()->addDays(30)->setTime(21, 0),
                'price' => 17.00,
                'description' => 'Luna Barcelona lleva su indie pop a Málaga. Una noche de emociones en el histórico Teatro Cervantes.',
                'capacity' => 1200,
                'genre' => 'Indie Pop',
            ],
            [
                'musician_profile_id' => $m3?->id ?? 3,
                'venue' => 'Auditorio Conde Duque',
                'city' => 'Madrid',
                'province' => 'Madrid',
                'autonomous_community' => 'Madrid',
                'date' => now()->addDays(50)->setTime(20, 30),
                'price' => 25.00,
                'description' => 'Brisa Valenciana visita Madrid con un show especial de jazz fusión en el acogedor Auditorio Conde Duque.',
                'capacity' => 800,
                'genre' => 'Jazz Fusión',
            ],
            [
                'musician_profile_id' => $m1?->id ?? 1,
                'venue' => 'Zentral',
                'city' => 'Bilbao',
                'province' => 'Vizcaya',
                'autonomous_community' => 'País Vasco',
                'date' => now()->addDays(55)->setTime(21, 0),
                'price' => 16.00,
                'description' => 'Indie pop llega al País Vasco. Luna Barcelona en la mítica sala Zentral de Bilbao.',
                'capacity' => 1200,
                'genre' => 'Indie Pop',
            ],
            [
                'musician_profile_id' => $m2?->id ?? 2,
                'venue' => 'Auditorio de Zaragoza',
                'city' => 'Zaragoza',
                'province' => 'Zaragoza',
                'autonomous_community' => 'Aragón',
                'date' => now()->addDays(18)->setTime(21, 0),
                'price' => 19.00,
                'description' => 'Rock con mayúsculas en Zaragoza. Trueno Madrid no defrauda en el Auditorio.',
                'capacity' => 2500,
                'genre' => 'Rock Alternativo',
            ],
            [
                'musician_profile_id' => $m3?->id ?? 3,
                'venue' => 'Sala Oasis',
                'city' => 'Alicante',
                'province' => 'Alicante',
                'autonomous_community' => 'Valencia',
                'date' => now()->addDays(8)->setTime(22, 0),
                'price' => 14.00,
                'description' => 'Jazz Fusión en Alicante. Brisa Valenciana regresa a sus raíces mediterráneas.',
                'capacity' => 600,
                'genre' => 'Jazz Fusión',
            ],
            [
                'musician_profile_id' => $m1?->id ?? 1,
                'venue' => 'Sala Viena',
                'city' => 'Santiago de Compostela',
                'province' => 'A Coruña',
                'autonomous_community' => 'Galicia',
                'date' => now()->addDays(70)->setTime(21, 30),
                'price' => 13.00,
                'description' => 'Indie pop en la ciudad del Camino. Luna Barcelona llega a Galicia por primera vez.',
                'capacity' => 500,
                'genre' => 'Indie Pop',
            ],
        ];

        foreach ($concerts as $concert) {
            Concert::create($concert);
        }
    }
}
