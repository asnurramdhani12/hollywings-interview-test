<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $class = [
            UserSeeder::class,
            CategoryBookSeeder::class,
            BookSeeder::class
        ];

        foreach ($class as $class) {
            $this->call($class);
        }
    }
}
