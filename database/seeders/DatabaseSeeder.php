<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MusicianProfile;
use App\Models\Song;
use App\Models\Concert;
use App\Models\Merch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Credenciales de acceso para cada músico:
     * ─────────────────────────────────────────────────────────────────
     *  Luna Barcelona   → luna@pulse.com       / password
     *  Trueno Madrid    → trueno@pulse.com     / password
     *  Brisa Valenciana → brisa@pulse.com      / password
     *  Luis Jazz        → luis@pulse.com       / password
     *  Usuario normal   → user@pulse.com       / password
     * ─────────────────────────────────────────────────────────────────
     */
    public function run(): void
    {
        // ══════════════════════════════════════════════════════════════
        // 1. USUARIOS (cada músico tiene su propio correo y contraseña)
        // ══════════════════════════════════════════════════════════════

        // Luna Barcelona
        $userLuna = User::create([
            'name' => 'Luna Barcelona',      // nombre artístico = nombre de usuario
            'email' => 'luna@pulse.com',
            'password' => Hash::make('password'),
            'role' => 'musician',
        ]);

        // Trueno Madrid
        $userTrueno = User::create([
            'name' => 'Trueno Madrid',
            'email' => 'trueno@pulse.com',
            'password' => Hash::make('password'),
            'role' => 'musician',
        ]);

        // Brisa Valenciana
        $userBrisa = User::create([
            'name' => 'Brisa Valenciana',
            'email' => 'brisa@pulse.com',
            'password' => Hash::make('password'),
            'role' => 'musician',
        ]);

        // Luis Jazz – tenía role=musician pero SIN perfil. Ahora tiene ambos.
        $userLuis = User::create([
            'name' => 'Luis Jazz',
            'email' => 'luis@pulse.com',
            'password' => Hash::make('password'),
            'role' => 'musician',
        ]);

        // Usuario normal (sin perfil artístico)
        User::create([
            'name' => 'Usuario Test',
            'email' => 'user@pulse.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // ══════════════════════════════════════════════════════════════
        // 2. PERFILES ARTÍSTICOS (uno por cada usuario músico)
        //    Regla: si eres músico, TIENES que tener MusicianProfile.
        // ══════════════════════════════════════════════════════════════

        $musician1 = MusicianProfile::create([
            'user_id' => $userLuna->id,
            'stage_name' => 'Luna Barcelona',
            'city' => 'Barcelona',
            'province' => 'Barcelona',
            'autonomous_community' => 'Cataluña',
            'genre' => 'Indie Pop',
            'bio' => 'Banda de indie pop nacida en las calles de Gràcia. Melodías suaves y letras profundas.',
        ]);

        $musician2 = MusicianProfile::create([
            'user_id' => $userTrueno->id,
            'stage_name' => 'Trueno Madrid',
            'city' => 'Madrid',
            'province' => 'Madrid',
            'autonomous_community' => 'Madrid',
            'genre' => 'Rock Alternativo',
            'bio' => 'Potencia y distorsión desde el corazón de Malasaña.',
        ]);

        $musician3 = MusicianProfile::create([
            'user_id' => $userBrisa->id,
            'stage_name' => 'Brisa Valenciana',
            'city' => 'Valencia',
            'province' => 'Valencia',
            'autonomous_community' => 'Valencia',
            'genre' => 'Jazz Fusión',
            'bio' => 'Mezclando ritmos mediterráneos con jazz clásico.',
        ]);

        $musician4 = MusicianProfile::create([
            'user_id' => $userLuis->id,
            'stage_name' => 'Luis Jazz',
            'city' => 'Sevilla',
            'province' => 'Sevilla',
            'autonomous_community' => 'Andalucía',
            'genre' => 'Jazz',
            'bio' => 'Jazz clásico con alma flamenca desde Sevilla. Noches de improvisación en los mejores clubs.',
        ]);

        // ══════════════════════════════════════════════════════════════
        // 3. CANCIONES DE DEMOSTRACIÓN
        // ══════════════════════════════════════════════════════════════

        Song::create(['musician_profile_id' => $musician1->id, 'title' => 'Noche en Gràcia', 'file_path' => 'dummy_song.mp3', 'royalties' => 0.00]);
        Song::create(['musician_profile_id' => $musician1->id, 'title' => 'Sueños de Verano', 'file_path' => 'dummy_song.mp3', 'royalties' => 0.00]);
        Song::create(['musician_profile_id' => $musician2->id, 'title' => 'Ruido Urbano', 'file_path' => 'dummy_song.mp3', 'royalties' => 0.00]);
        Song::create(['musician_profile_id' => $musician2->id, 'title' => 'Malasaña en Llamas', 'file_path' => 'dummy_song.mp3', 'royalties' => 0.00]);
        Song::create(['musician_profile_id' => $musician3->id, 'title' => 'Mar y Fuego', 'file_path' => 'dummy_song.mp3', 'royalties' => 0.00]);
        Song::create(['musician_profile_id' => $musician4->id, 'title' => 'Bulerías del Alba', 'file_path' => 'dummy_song.mp3', 'royalties' => 0.00]);
        Song::create(['musician_profile_id' => $musician4->id, 'title' => 'Sevilla de Noche', 'file_path' => 'dummy_song.mp3', 'royalties' => 0.00]);

        // ══════════════════════════════════════════════════════════════
        // 4. CONCIERTOS BASE (el ConcertSeeder añade 15 más)
        // ══════════════════════════════════════════════════════════════

        Concert::create([
            'musician_profile_id' => $musician1->id,
            'venue' => 'Sala Apolo',
            'city' => 'Barcelona',
            'province' => 'Barcelona',
            'autonomous_community' => 'Cataluña',
            'date' => now()->addDays(10)->setTime(21, 0),
            'price' => 15.00,
            'description' => 'Luna Barcelona presenta su álbum debut en la emblemática Sala Apolo.',
            'capacity' => 900,
            'genre' => 'Indie Pop',
        ]);

        Concert::create([
            'musician_profile_id' => $musician2->id,
            'venue' => 'La Riviera',
            'city' => 'Madrid',
            'province' => 'Madrid',
            'autonomous_community' => 'Comunidad de Madrid',
            'date' => now()->addDays(5)->setTime(21, 0),
            'price' => 20.00,
            'description' => 'Rock alternativo a todo volumen en uno de los recintos más icónicos de Madrid.',
            'capacity' => 1800,
            'genre' => 'Rock Alternativo',
        ]);

        Concert::create([
            'musician_profile_id' => $musician4->id,
            'venue' => 'Sala fun&basics',
            'city' => 'Sevilla',
            'province' => 'Sevilla',
            'autonomous_community' => 'Andalucía',
            'date' => now()->addDays(12)->setTime(22, 0),
            'price' => 12.00,
            'description' => 'Jazz flamenco en directo. Luis Jazz presenta "Bulerías del Alba" en Sevilla.',
            'capacity' => 400,
            'genre' => 'Jazz',
        ]);

        // ══════════════════════════════════════════════════════════════
        // 5. MERCHANDISING
        // ══════════════════════════════════════════════════════════════

        $items = [
            // Luna Barcelona
            ['musician_profile_id' => $musician1->id, 'name' => 'Camiseta Luna Barcelona', 'description' => 'Camiseta algodón 100% con diseño exclusivo.', 'price' => 24.99, 'category' => 'Camisetas', 'city' => 'Barcelona', 'sales_count' => 142],
            ['musician_profile_id' => $musician1->id, 'name' => 'Sudadera Gràcia Nights', 'description' => 'Sudadera premium con estampado nocturno de Barcelona.', 'price' => 44.99, 'category' => 'Sudaderas', 'city' => 'Barcelona', 'sales_count' => 78],
            ['musician_profile_id' => $musician1->id, 'name' => 'Gorra Luna BCN', 'description' => 'Gorra snapback con logo bordado.', 'price' => 19.99, 'category' => 'Gorras', 'city' => 'Barcelona', 'sales_count' => 205],
            ['musician_profile_id' => $musician1->id, 'name' => 'Póster Sueños de Verano', 'description' => 'Póster A2 edición limitada de su álbum debut.', 'price' => 12.99, 'category' => 'Pósters', 'city' => 'Barcelona', 'sales_count' => 310],
            // Trueno Madrid
            ['musician_profile_id' => $musician2->id, 'name' => 'Camiseta Trueno Oversize', 'description' => 'Camiseta negra oversize con logo distorsionado.', 'price' => 29.99, 'category' => 'Camisetas', 'city' => 'Madrid', 'sales_count' => 95],
            ['musician_profile_id' => $musician2->id, 'name' => 'Sudadera Malasaña Hardcore', 'description' => 'Sudadera con capucha, edición Malasaña Tour.', 'price' => 49.99, 'category' => 'Sudaderas', 'city' => 'Madrid', 'sales_count' => 61],
            ['musician_profile_id' => $musician2->id, 'name' => 'Gorra Trueno 5 Panel', 'description' => 'Gorra 5 paneles con estampado de rayo frontal.', 'price' => 22.99, 'category' => 'Gorras', 'city' => 'Madrid', 'sales_count' => 134],
            ['musician_profile_id' => $musician2->id, 'name' => 'Bolsa Trueno Tote', 'description' => 'Bolsa de tela resistente con diseño de Trueno Madrid.', 'price' => 14.99, 'category' => 'Bolsas', 'city' => 'Madrid', 'sales_count' => 187],
            // Brisa Valenciana
            ['musician_profile_id' => $musician3->id, 'name' => 'Camiseta Mar y Sol', 'description' => 'Camiseta de verano con estampado mediterráneo.', 'price' => 21.99, 'category' => 'Camisetas', 'city' => 'Valencia', 'sales_count' => 112],
            ['musician_profile_id' => $musician3->id, 'name' => 'Sudadera Jazz Fusión', 'description' => 'Sudadera ligera con notas musicales bordadas.', 'price' => 38.99, 'category' => 'Sudaderas', 'city' => 'Valencia', 'sales_count' => 43],
            ['musician_profile_id' => $musician3->id, 'name' => 'Póster Mar y Fuego', 'description' => 'Póster ilustrado A1, numerado y firmado.', 'price' => 18.99, 'category' => 'Pósters', 'city' => 'Valencia', 'sales_count' => 256],
            ['musician_profile_id' => $musician3->id, 'name' => 'Bolsa Brisa Valencia', 'description' => 'Bolsa tote con ilustración de la costa valenciana.', 'price' => 13.99, 'category' => 'Bolsas', 'city' => 'Valencia', 'sales_count' => 88],
            // Luis Jazz
            ['musician_profile_id' => $musician4->id, 'name' => 'Camiseta Luis Jazz', 'description' => 'Camiseta con diseño de trompeta y motivos flamencos.', 'price' => 22.99, 'category' => 'Camisetas', 'city' => 'Sevilla', 'sales_count' => 67],
            ['musician_profile_id' => $musician4->id, 'name' => 'Póster Bulerías del Alba', 'description' => 'Póster ilustrado edición especial del álbum debut.', 'price' => 14.99, 'category' => 'Pósters', 'city' => 'Sevilla', 'sales_count' => 94],
        ];

        foreach ($items as $item) {
            Merch::create($item);
        }

        // ══════════════════════════════════════════════════════════════
        // 6. MÚSICOS ADICIONALES — hasta completar 50 bandas/artistas
        // ══════════════════════════════════════════════════════════════
        $extraMusicians = [
            // Andalucía
            ['name'=>'Flamenco Sur',      'email'=>'flamencosur@pulse.com',      'city'=>'Málaga',       'province'=>'Málaga',       'community'=>'Andalucía',              'genre'=>'Flamenco',        'bio'=>'Raíces flamencas de la Costa del Sol.'],
            ['name'=>'Rumba Granadina',   'email'=>'rumbagranadina@pulse.com',   'city'=>'Granada',      'province'=>'Granada',      'community'=>'Andalucía',              'genre'=>'Rumba',           'bio'=>'La rumba más auténtica al pie de la Alhambra.'],
            ['name'=>'Copla Andaluza',    'email'=>'copla@pulse.com',            'city'=>'Cádiz',        'province'=>'Cádiz',        'community'=>'Andalucía',              'genre'=>'Copla',           'bio'=>'Copla y canción española desde la bahía de Cádiz.'],
            ['name'=>'Solea Project',     'email'=>'solea@pulse.com',            'city'=>'Córdoba',      'province'=>'Córdoba',      'community'=>'Andalucía',              'genre'=>'Flamenco Fusión', 'bio'=>'Flamenco contemporáneo desde Córdoba.'],
            // Aragón
            ['name'=>'Jota Brava',        'email'=>'jotabrava@pulse.com',        'city'=>'Zaragoza',     'province'=>'Zaragoza',     'community'=>'Aragón',                 'genre'=>'Folk',            'bio'=>'La jota aragonesa llevada al siglo XXI.'],
            ['name'=>'Ibero Rock',        'email'=>'iberorock@pulse.com',        'city'=>'Huesca',       'province'=>'Huesca',       'community'=>'Aragón',                 'genre'=>'Rock',            'bio'=>'Rock de los Pirineos con fuerte identidad local.'],
            // Asturias
            ['name'=>'Gaita Eléctrica',   'email'=>'gaitaelectrica@pulse.com',   'city'=>'Oviedo',       'province'=>'Asturias',     'community'=>'Asturias',               'genre'=>'Folk Rock',       'bio'=>'Gaita asturiana con distorsión eléctrica.'],
            ['name'=>'Sidra y Blues',     'email'=>'sidrablues@pulse.com',       'city'=>'Gijón',        'province'=>'Asturias',     'community'=>'Asturias',               'genre'=>'Blues',           'bio'=>'Blues norteño con sabor a sidra natural.'],
            // Canarias
            ['name'=>'Timple Sessions',   'email'=>'timple@pulse.com',           'city'=>'Las Palmas',   'province'=>'Las Palmas',   'community'=>'Canarias',               'genre'=>'Folk Canario',    'bio'=>'El timple canario en el centro de la escena indie.'],
            ['name'=>'Alisio',            'email'=>'alisio@pulse.com',           'city'=>'Santa Cruz',   'province'=>'Santa Cruz de Tenerife','community'=>'Canarias',       'genre'=>'Reggae',          'bio'=>'Reggae atlántico desde las islas.'],
            // Cantabria
            ['name'=>'Mar Cantábrico',    'email'=>'marcantabrico@pulse.com',    'city'=>'Santander',    'province'=>'Cantabria',    'community'=>'Cantabria',              'genre'=>'Indie',           'bio'=>'Indie melancólico inspirado en el mar del norte.'],
            // Castilla-La Mancha
            ['name'=>'La Mancha Sound',   'email'=>'lamanchasound@pulse.com',    'city'=>'Albacete',     'province'=>'Albacete',     'community'=>'Castilla-La Mancha',     'genre'=>'Pop Rock',        'bio'=>'Sonido manchego moderno y sin complejos.'],
            ['name'=>'Quijote Beats',     'email'=>'quijotebeats@pulse.com',     'city'=>'Toledo',       'province'=>'Toledo',       'community'=>'Castilla-La Mancha',     'genre'=>'Hip-Hop',         'bio'=>'Hip-hop desde la tierra de Cervantes.'],
            // Castilla y León
            ['name'=>'Meseta Club',       'email'=>'meseta@pulse.com',           'city'=>'Valladolid',   'province'=>'Valladolid',   'community'=>'Castilla y León',        'genre'=>'Indie Pop',       'bio'=>'Indie de llanura con letras literarias.'],
            ['name'=>'Duero Sessions',    'email'=>'duero@pulse.com',            'city'=>'Salamanca',    'province'=>'Salamanca',    'community'=>'Castilla y León',        'genre'=>'Folk',            'bio'=>'Canciones a orillas del Duero, voz y guitarra.'],
            ['name'=>'Soria Noise',       'email'=>'sorianoise@pulse.com',       'city'=>'Soria',        'province'=>'Soria',        'community'=>'Castilla y León',        'genre'=>'Noise Rock',      'bio'=>'Ruidismo minimalista desde la ciudad más pequeña.'],
            // Cataluña
            ['name'=>'Barceloneta Sound', 'email'=>'barceloneta@pulse.com',      'city'=>'Barcelona',    'province'=>'Barcelona',    'community'=>'Cataluña',               'genre'=>'Electro',         'bio'=>'Electrónica de playa desde Barceloneta.'],
            ['name'=>'Girona Folk',       'email'=>'gironafolk@pulse.com',       'city'=>'Girona',       'province'=>'Girona',       'community'=>'Cataluña',               'genre'=>'Folk',            'bio'=>'Canciones en catalán con aromas medievales.'],
            ['name'=>'Tarragona Blues',   'email'=>'tarragonablues@pulse.com',   'city'=>'Tarragona',    'province'=>'Tarragona',    'community'=>'Cataluña',               'genre'=>'Blues',           'bio'=>'Blues romano desde la costa de Tarragona.'],
            // Extremadura
            ['name'=>'Dehesa Sound',      'email'=>'dehesa@pulse.com',           'city'=>'Badajoz',      'province'=>'Badajoz',      'community'=>'Extremadura',            'genre'=>'Folk Rock',       'bio'=>'Canciones inspiradas en la dehesa extremeña.'],
            // Galicia
            ['name'=>'Celtia',            'email'=>'celtia@pulse.com',           'city'=>'Santiago de Compostela','province'=>'A Coruña','community'=>'Galicia',            'genre'=>'Celtic Folk',     'bio'=>'Música celta desde el corazón de Galicia.'],
            ['name'=>'Ría Vigo',          'email'=>'riavigo@pulse.com',          'city'=>'Vigo',         'province'=>'Pontevedra',   'community'=>'Galicia',                'genre'=>'Indie Rock',      'bio'=>'Rock atlántico desde las rías gallegas.'],
            // La Rioja
            ['name'=>'Ribera Rock',       'email'=>'riberarock@pulse.com',       'city'=>'Logroño',      'province'=>'La Rioja',     'community'=>'La Rioja',               'genre'=>'Rock',            'bio'=>'Rock de vendimia desde La Rioja.'],
            // Madrid
            ['name'=>'Gran Vía Noise',    'email'=>'granvianoise@pulse.com',     'city'=>'Madrid',       'province'=>'Madrid',       'community'=>'Comunidad de Madrid',    'genre'=>'Punk',            'bio'=>'Punk descarnado desde el centro de Madrid.'],
            ['name'=>'Rastro Sessions',   'email'=>'rastro@pulse.com',           'city'=>'Madrid',       'province'=>'Madrid',       'community'=>'Comunidad de Madrid',    'genre'=>'Soul',            'bio'=>'Soul vintage encontrado en el mercado del Rastro.'],
            ['name'=>'Lavapiés Beat',     'email'=>'lavapies@pulse.com',         'city'=>'Madrid',       'province'=>'Madrid',       'community'=>'Comunidad de Madrid',    'genre'=>'Rap',             'bio'=>'Rap multicultural desde el corazón de Lavapiés.'],
            // Murcia
            ['name'=>'Huerta Sound',      'email'=>'huerta@pulse.com',           'city'=>'Murcia',       'province'=>'Murcia',       'community'=>'Murcia',                 'genre'=>'Pop',             'bio'=>'Pop mediterráneo desde la huerta murciana.'],
            // Navarra
            ['name'=>'Sanfermín Beats',   'email'=>'sanfermin@pulse.com',        'city'=>'Pamplona',     'province'=>'Navarra',      'community'=>'Navarra',                'genre'=>'Indie',           'bio'=>'Indie navarro que suena a encierro y libertad.'],
            // País Vasco
            ['name'=>'Txalaparta Club',   'email'=>'txalaparta@pulse.com',       'city'=>'Bilbao',       'province'=>'Bizkaia',      'community'=>'País Vasco',             'genre'=>'Post-Rock',       'bio'=>'Post-rock vasco con percusión tradicional.'],
            ['name'=>'Guggenheim Noise',  'email'=>'guggenheim@pulse.com',       'city'=>'Bilbao',       'province'=>'Bizkaia',      'community'=>'País Vasco',             'genre'=>'Electro Rock',    'bio'=>'Electro-rock al pie del Guggenheim.'],
            ['name'=>'Donosti Pop',       'email'=>'donostipop@pulse.com',       'city'=>'San Sebastián','province'=>'Gipuzkoa',     'community'=>'País Vasco',             'genre'=>'Pop',             'bio'=>'Pop sofisticado con rastros de bossa nova.'],
            // Valencia
            ['name'=>'Fallas Rock',       'email'=>'fallasrock@pulse.com',       'city'=>'Valencia',     'province'=>'Valencia',     'community'=>'Comunitat Valenciana',   'genre'=>'Rock',            'bio'=>'Rock explosivo con pirotecnia sónica.'],
            ['name'=>'Paella Sessions',   'email'=>'paella@pulse.com',           'city'=>'Alicante',     'province'=>'Alicante',     'community'=>'Comunitat Valenciana',   'genre'=>'Indie Pop',       'bio'=>'Indie costero desde las playas de Alicante.'],
            ['name'=>'Castelló Waves',    'email'=>'castello@pulse.com',         'city'=>'Castellón',    'province'=>'Castellón',    'community'=>'Comunitat Valenciana',   'genre'=>'Surf Rock',       'bio'=>'Surf rock mediterráneo desde Castellón.'],
            // Baleares
            ['name'=>'Ibiza Underground', 'email'=>'ibizaunderground@pulse.com', 'city'=>'Ibiza',        'province'=>'Ibiza',        'community'=>'Islas Baleares',         'genre'=>'Electronic',      'bio'=>'Electrónica underground lejos del turismo masivo.'],
            ['name'=>'Mallorca Folk',     'email'=>'mallorcafolk@pulse.com',     'city'=>'Palma',        'province'=>'Mallorca',     'community'=>'Islas Baleares',         'genre'=>'Folk',            'bio'=>'Canciones a la sombra de los almendros mallorquines.'],
            // Cantabria extra, Murcia extra, extra variety
            ['name'=>'Albacete Noise',    'email'=>'albacetenoise@pulse.com',    'city'=>'Albacete',     'province'=>'Albacete',     'community'=>'Castilla-La Mancha',     'genre'=>'Shoegaze',        'bio'=>'Shoegaze manchego de capas y capas de reverb.'],
            ['name'=>'Córdoba Jazz Club', 'email'=>'cordobajazz@pulse.com',      'city'=>'Córdoba',      'province'=>'Córdoba',      'community'=>'Andalucía',              'genre'=>'Jazz',            'bio'=>'Clásicos del jazz con alma andaluza.'],
            ['name'=>'Vitoria Indie',     'email'=>'vitoria@pulse.com',          'city'=>'Vitoria',      'province'=>'Álava',        'community'=>'País Vasco',             'genre'=>'Indie',           'bio'=>'Indie tranquilo desde la tranquila capital vasca.'],
            ['name'=>'Segovia Folk',      'email'=>'segovia@pulse.com',          'city'=>'Segovia',      'province'=>'Segovia',      'community'=>'Castilla y León',        'genre'=>'Folk',            'bio'=>'Canciones al pie del acueducto.'],
            ['name'=>'Cuenca Ambient',    'email'=>'cuenca@pulse.com',           'city'=>'Cuenca',       'province'=>'Cuenca',       'community'=>'Castilla-La Mancha',     'genre'=>'Ambient',         'bio'=>'Música etérea que suena a casas colgadas.'],
            ['name'=>'Huelva Soul',       'email'=>'huelva@pulse.com',           'city'=>'Huelva',       'province'=>'Huelva',       'community'=>'Andalucía',              'genre'=>'Soul',            'bio'=>'Soul mestizo con herencia colombina.'],
            ['name'=>'Burgos Metal',      'email'=>'burgos@pulse.com',           'city'=>'Burgos',       'province'=>'Burgos',       'community'=>'Castilla y León',        'genre'=>'Metal',           'bio'=>'Metal gótico desde la catedral de Burgos.'],
            ['name'=>'Lleida Country',    'email'=>'lleida@pulse.com',           'city'=>'Lleida',       'province'=>'Lleida',       'community'=>'Cataluña',               'genre'=>'Country',         'bio'=>'Country de secano desde las tierras de Lleida.'],
        ];

        $songTitles = [
            'Entre sombras', 'Luz de ciudad', 'El último tren', 'Marea alta', 'Polvo de estrellas',
            'Viento del norte', 'Raíces', 'Sonata urbana', 'Tarde de octubre', 'El silencio habla',
            'Noche cerrada', 'Destellos', 'Camino antiguo', 'Río profundo', 'Alba nueva',
        ];

        foreach ($extraMusicians as $i => $data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make('password'),
                'role'     => 'musician',
            ]);

            $profile = MusicianProfile::create([
                'user_id'             => $user->id,
                'stage_name'          => $data['name'],
                'city'                => $data['city'],
                'province'            => $data['province'],
                'autonomous_community'=> $data['community'],
                'genre'               => $data['genre'],
                'bio'                 => $data['bio'],
            ]);

            // 2 songs per artist
            Song::create([
                'musician_profile_id' => $profile->id,
                'title'    => $songTitles[$i % count($songTitles)],
                'file_path'=> 'dummy_song.mp3',
                'royalties' => 0.00,
            ]);
            Song::create([
                'musician_profile_id' => $profile->id,
                'title'    => $songTitles[($i + 7) % count($songTitles)],
                'file_path'=> 'dummy_song.mp3',
                'royalties' => 0.00,
            ]);
        }
    }
}
