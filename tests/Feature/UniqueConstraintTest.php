<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniqueConstraintTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_slug_and_name_are_unique(): void
    {
        Category::create([
            'name' => 'Fiksi',
            'slug' => 'fiksi',
        ]);

        $this->expectException(QueryException::class);

        Category::create([
            'name' => 'Fiksi',
            'slug' => 'fiksi',
        ]);
    }

    public function test_book_isbn_is_unique(): void
    {
        $cat = Category::create([
            'name' => 'Komik',
            'slug' => 'komik',
        ]);

        Book::create([
            'category_id' => $cat->id,
            'title' => 'Komik 1',
            'slug' => 'komik-1',
            'author' => 'Author 1',
            'isbn' => '9780000000001',
            'price' => 50000,
            'stock' => 10,
        ]);

        $this->expectException(QueryException::class);

        Book::create([
            'category_id' => $cat->id,
            'title' => 'Komik 2',
            'slug' => 'komik-2',
            'author' => 'Author 2',
            'isbn' => '9780000000001',
            'price' => 60000,
            'stock' => 5,
        ]);
    }
}
