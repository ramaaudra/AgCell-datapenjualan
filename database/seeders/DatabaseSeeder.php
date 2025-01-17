<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        //call all seeders
        $this->call([
            KategoriSeeder::class,
            ProdukSeeder::class,
            PenjualanSeeder::class,
            PengeluaranSeeder::class,
        ]);
    }
}
