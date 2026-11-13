<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistAndReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_live_search_returns_json_suggestions(): void
    {
        $category = Category::factory()->create();
        Book::factory()->create([
            'category_id' => $category->id,
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'slug' => 'laskar-pelangi',
        ]);

        $response = $this->getJson('/api/search?q=Laskar');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
            ]);
    }

    public function test_user_can_toggle_book_wishlist(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        // 1. Add to wishlist
        $response = $this->actingAs($user)->post(route('wishlist.toggle', $book));
        $response->assertRedirect();
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        // 2. Remove from wishlist by toggling again
        $response = $this->actingAs($user)->post(route('wishlist.toggle', $book));
        $response->assertRedirect();
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_user_can_view_wishlist_page(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);
        Wishlist::create(['user_id' => $user->id, 'book_id' => $book->id]);

        $response = $this->actingAs($user)->get(route('wishlist.index'));

        $response->assertStatus(200)
            ->assertSee($book->title);
    }

    public function test_user_can_submit_book_review(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->post(route('books.reviews.store', $book), [
            'rating' => 5,
            'comment' => 'Buku yang sangat menginspirasi dan penuh makna!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'comment' => 'Buku yang sangat menginspirasi dan penuh makna!',
        ]);

        // Cannot review again (duplicate check)
        $duplicateResponse = $this->actingAs($user)->post(route('books.reviews.store', $book), [
            'rating' => 4,
            'comment' => 'Ulasan kedua yang tidak boleh masuk.',
        ]);

        $duplicateResponse->assertSessionHas('error');
        $this->assertCount(1, Review::where('user_id', $user->id)->where('book_id', $book->id)->get());
    }
}
