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

    public function test_user_can_update_own_review(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['category_id' => Category::factory()->create()->id]);
        $review = Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 3,
            'comment' => 'Awalnya biasa saja, tapi setelah dibaca ulang cukup bagus.',
        ]);

        $response = $this->actingAs($user)->put(route('books.reviews.update', [$book, $review]), [
            'rating' => 5,
            'comment' => 'Setelah dibaca ulang ternyata sangat bagus!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 5,
            'comment' => 'Setelah dibaca ulang ternyata sangat bagus!',
        ]);
    }

    public function test_user_cannot_edit_or_delete_another_users_review(): void
    {
        $author = User::factory()->create();
        $intruder = User::factory()->create();
        $book = Book::factory()->create(['category_id' => Category::factory()->create()->id]);
        $review = Review::create([
            'user_id' => $author->id,
            'book_id' => $book->id,
            'rating' => 4,
            'comment' => 'Ulasan asli milik pembaca lain.',
        ]);

        $this->actingAs($intruder)
            ->put(route('books.reviews.update', [$book, $review]), [
                'rating' => 1,
                'comment' => 'Ulasan palsu yang ditimpa orang lain.',
            ])
            ->assertStatus(403);

        $this->actingAs($intruder)
            ->delete(route('books.reviews.destroy', [$book, $review]))
            ->assertStatus(403);

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 4,
            'comment' => 'Ulasan asli milik pembaca lain.',
        ]);
    }

    public function test_owner_and_admin_can_delete_a_review(): void
    {
        $author = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $book = Book::factory()->create(['category_id' => Category::factory()->create()->id]);

        $ownReview = Review::create([
            'user_id' => $author->id,
            'book_id' => $book->id,
            'rating' => 2,
            'comment' => 'Kertasnya tipis dan tintanya luntur.',
        ]);

        $this->actingAs($author)
            ->delete(route('books.reviews.destroy', [$book, $ownReview]))
            ->assertRedirect();
        $this->assertDatabaseMissing('reviews', ['id' => $ownReview->id]);

        $otherReview = Review::create([
            'user_id' => $author->id,
            'book_id' => $book->id,
            'rating' => 1,
            'comment' => 'Ulasan yang perlu dimoderasi oleh admin.',
        ]);

        $this->actingAs($admin)
            ->delete(route('books.reviews.destroy', [$book, $otherReview]))
            ->assertRedirect();
        $this->assertDatabaseMissing('reviews', ['id' => $otherReview->id]);
    }

    public function test_book_detail_page_paginates_reviews(): void
    {
        $book = Book::factory()->create(['category_id' => Category::factory()->create()->id]);

        foreach (range(1, 7) as $i) {
            Review::create([
                'user_id' => User::factory()->create()->id,
                'book_id' => $book->id,
                'rating' => 4,
                'comment' => "Ulasan pembaca nomor {$i} yang cukup panjang untuk diuji.",
            ]);
        }

        $firstPage = $this->get(route('books.show', $book->slug));
        $firstPage->assertStatus(200)->assertSee('Ulasan pembaca nomor');
        $this->assertCount(5, $firstPage->viewData('reviews'));

        $secondPage = $this->get(route('books.show', $book->slug).'?page=2');
        $secondPage->assertStatus(200)->assertSee('Ulasan pembaca nomor');
        $this->assertCount(2, $secondPage->viewData('reviews'));
    }
}
