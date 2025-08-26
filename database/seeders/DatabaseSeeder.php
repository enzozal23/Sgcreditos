<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            VariablesSeeder::class,
            // ClienteSeeder::class, // Comentado por error de columna ciudad
            ProvinciaSeeder::class, // Agregar el seeder de provincias
            LocalidadSeeder::class, // Agregar el seeder de localidades
            TipoAmortizacionSeeder::class, // Agregar el seeder de tipos de amortización
        ]);
    }
}
