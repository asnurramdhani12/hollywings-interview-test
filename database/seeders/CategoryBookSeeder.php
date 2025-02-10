<?php

namespace Database\Seeders;

use App\Models\CategoryBook;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryBook = [
            [
                "category_name" => "Novel"
            ],
            [
                "category_name" => "Comics"
            ],
            [
                "category_name" => "Fiction"
            ],
            [
                "category_name" => "Horror"
            ],
            [
                "category_name" => "Fantasy"
            ],
            [
                "category_name" => "Mystery"
            ],
            [
                "category_name" => "Thriller"
            ],
            [
                "category_name" => "Sci-Fi"
            ],
        ];

        foreach ($categoryBook as $category) {
            CategoryBook::create($category);
        }
    }
}
