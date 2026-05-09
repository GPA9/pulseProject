<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubscriptionPlan::create([
            'name' => 'Básico',
            'price' => 0.99,
            'max_albums' => 1,
            'billing_cycle' => 'one-time',
            'description' => 'Sube 1 álbum con todas tus canciones',
        ]);

        SubscriptionPlan::create([
            'name' => 'Estándar',
            'price' => 2.99,
            'max_albums' => 5,
            'billing_cycle' => 'monthly',
            'description' => 'Sube hasta 5 álbumes completos',
        ]);

        SubscriptionPlan::create([
            'name' => 'Premium',
            'price' => 10.00,
            'max_albums' => 999,
            'billing_cycle' => 'monthly',
            'description' => 'Sube ilimitados álbumes (plan mensual)',
        ]);
    }
}
