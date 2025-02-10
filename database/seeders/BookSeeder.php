<?php

namespace Database\Seeders;

use App\Models\Books;
use App\Models\CategoryBook;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            ["title" => "Book A", "author" => "Author 1", "description" => "Description A", "image" => "book_a.jpg", "category_name" => "Novel", "stock" => 10],
            ["title" => "Book B", "author" => "Author 2", "description" => "Description B", "image" => "book_b.jpg", "category_name" => "Comics", "stock" => 5],
            ["title" => "Book C", "author" => "Author 3", "description" => "Description C", "image" => "book_c.jpg", "category_name" => "Fiction", "stock" => 20],
            ["title" => "Book D", "author" => "Author 4", "description" => "Description D", "image" => "book_d.jpg", "category_name" => "Horror", "stock" => 15],
            ["title" => "Book E", "author" => "Author 5", "description" => "Description E", "image" => "book_e.jpg", "category_name" => "Fantasy", "stock" => 8],
            ["title" => "Book F", "author" => "Author 6", "description" => "Description F", "image" => "book_f.jpg", "category_name" => "Mystery", "stock" => 12],
            ["title" => "Book G", "author" => "Author 7", "description" => "Description G", "image" => "book_g.jpg", "category_name" => "Thriller", "stock" => 18],
            ["title" => "Book H", "author" => "Author 8", "description" => "Description H", "image" => "book_h.jpg", "category_name" => "Sci-Fi", "stock" => 9],
        ];

        // Insert books into the books table with category_id
        foreach ($books as &$book) {
            $category = CategoryBook::where('category_name', $book['category_name'])->first();
            if ($category) {
                $book['category_id'] = $category->id;
                unset($book['category_name']); // Remove category_name, since we only need category_id
                Books::create($book);
            }
        }
    }
}
