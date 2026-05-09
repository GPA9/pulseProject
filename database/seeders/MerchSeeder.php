<?php

namespace Database\Seeders;

use App\Models\Merch;
use App\Models\MusicianProfile;
use Illuminate\Database\Seeder;

class MerchSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing musician profiles by stage name
        $musician1 = MusicianProfile::where('stage_name', 'Luna Barcelona')->first();
        $musician2 = MusicianProfile::where('stage_name', 'Trueno Madrid')->first();
        $musician3 = MusicianProfile::where('stage_name', 'Brisa Valenciana')->first();

        if (!$musician1 || !$musician2 || !$musician3) {
            $this->command->warn('Musician profiles not found. Run DatabaseSeeder first.');
            return;
        }

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
        ];

        foreach ($items as $item) {
            Merch::updateOrCreate(
                ['musician_profile_id' => $item['musician_profile_id'], 'name' => $item['name']],
                $item
            );
        }

        $this->command->info('Merch items seeded successfully!');
    }
}
