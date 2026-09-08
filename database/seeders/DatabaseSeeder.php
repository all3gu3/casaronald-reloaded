<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CatalogoSeeder::class,
            GeografiaSeeder::class,
            MasterUserSeeder::class,
        ]);

        if (app()->environment('local')) {
            $this->call(DemoSeeder::class);
        }

        $this->command?->info('Semilla plantada 🌱');
    }
}
