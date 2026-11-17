<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAddressTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;

    private Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create(['role' => 'user']);
        $this->book = Book::factory()->create([
            'category_id' => Category::factory()->create()->id,
            'price' => 100000,
            'discount_percent' => 0,
            'stock' => 10,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function addressPayload(string $label = 'Rumah', string $address = 'Jl. Melati No. 3, Jakarta'): array
    {
        return [
            'label' => $label,
            'recipient_name' => 'Dimas Pratama',
            'phone' => '081298765432',
            'address' => $address,
            'city' => 'Kota Administrasi Jakarta Pusat',
            'postal_code' => '10310',
        ];
    }

    public function test_first_saved_address_becomes_the_default(): void
    {
        $this->actingAs($this->buyer)
            ->post(route('user.addresses.store'), $this->addressPayload() + ['is_default' => null])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('addresses', [
            'user_id' => $this->buyer->id,
            'label' => 'Rumah',
            'is_default' => true,
        ]);
    }

    public function test_only_one_address_stays_default(): void
    {
        $this->actingAs($this->buyer)->post(route('user.addresses.store'), $this->addressPayload('Rumah') + ['is_default' => '1']);
        $this->actingAs($this->buyer)->post(route('user.addresses.store'), $this->addressPayload('Kantor', 'Jl. Sudirman No. 8, Bandung') + ['is_default' => '1']);

        $this->assertSame(1, Address::where('user_id', $this->buyer->id)->where('is_default', true)->count());
        $this->assertDatabaseHas('addresses', ['label' => 'Rumah', 'is_default' => false]);
        $this->assertDatabaseHas('addresses', ['label' => 'Kantor', 'is_default' => true]);
    }

    public function test_user_can_update_and_delete_a_saved_address(): void
    {
        $address = Address::factory()->create(['user_id' => $this->buyer->id, 'label' => 'Rumah']);

        $this->actingAs($this->buyer)
            ->put(route('user.addresses.update', $address), $this->addressPayload('Pondok') + ['is_default' => '1'])
            ->assertRedirect();

        $address->refresh();
        $this->assertSame('Pondok', $address->label);
        $this->assertTrue($address->is_default);

        $this->actingAs($this->buyer)->delete(route('user.addresses.destroy', $address))->assertRedirect();
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_deleting_the_default_address_promotes_another_one(): void
    {
        $default = Address::factory()->create(['user_id' => $this->buyer->id, 'label' => 'Utama', 'is_default' => true]);
        $spare = Address::factory()->create(['user_id' => $this->buyer->id, 'label' => 'Cadangan', 'is_default' => false]);

        $this->actingAs($this->buyer)->delete(route('user.addresses.destroy', $default));

        $this->assertTrue($spare->refresh()->is_default);
    }

    public function test_switching_the_default_address_with_a_button(): void
    {
        $first = Address::factory()->create(['user_id' => $this->buyer->id, 'is_default' => true]);
        $second = Address::factory()->create(['user_id' => $this->buyer->id, 'is_default' => false]);

        $this->actingAs($this->buyer)->patch(route('user.addresses.default', $second))->assertRedirect();

        $this->assertFalse($first->refresh()->is_default);
        $this->assertTrue($second->refresh()->is_default);
    }

    public function test_users_cannot_modify_another_users_address(): void
    {
        $other = User::factory()->create(['role' => 'user']);
        $address = Address::factory()->create(['user_id' => $other->id]);

        $payload = $this->addressPayload();

        $this->actingAs($this->buyer)->put(route('user.addresses.update', $address), $payload)->assertForbidden();
        $this->actingAs($this->buyer)->delete(route('user.addresses.destroy', $address))->assertForbidden();
        $this->actingAs($this->buyer)->patch(route('user.addresses.default', $address))->assertForbidden();

        $this->assertDatabaseHas('addresses', ['id' => $address->id, 'user_id' => $other->id]);
    }

    public function test_guests_are_redirected_from_the_address_routes(): void
    {
        $this->post(route('user.addresses.store'), $this->addressPayload())->assertRedirect(route('login'));
    }

    public function test_profile_page_lists_saved_addresses(): void
    {
        Address::factory()->create([
            'user_id' => $this->buyer->id,
            'label' => 'Rumah Nenek',
            'address' => 'Jl. Cendana No. 7',
            'city' => 'Yogyakarta',
            'is_default' => true,
        ]);

        $this->actingAs($this->buyer)
            ->get(route('user.profile.edit'))
            ->assertOk()
            ->assertSee('Rumah Nenek')
            ->assertSee('Jl. Cendana No. 7')
            ->assertSee('Buku Alamat Pengiriman');
    }

    public function test_checkout_prefills_the_default_address(): void
    {
        $this->actingAs($this->buyer)->post(route('books.cart.add', $this->book), ['quantity' => 1]);

        Address::factory()->create([
            'user_id' => $this->buyer->id,
            'recipient_name' => 'Rina Kartika',
            'phone' => '081122334455',
            'address' => 'Jl. Kenanga No. 12',
            'city' => 'Bandung',
            'postal_code' => '40115',
            'is_default' => true,
        ]);

        $this->actingAs($this->buyer)
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('Rina Kartika')
            ->assertSee('Jl. Kenanga No. 12')
            ->assertSee('Pakai Alamat Tersimpan');
    }

    public function test_checkout_can_save_the_shipping_address_into_the_book(): void
    {
        $this->actingAs($this->buyer)->post(route('books.cart.add', $this->book), ['quantity' => 1]);

        $this->actingAs($this->buyer)->post(route('checkout.store'), [
            'recipient_name' => 'Rina Kartika',
            'phone' => '081122334455',
            'address' => 'Jl. Kenanga No. 12, Bandung',
            'city' => 'Bandung',
            'postal_code' => '40115',
            'address_label' => 'Kantor',
            'payment_method' => 'transfer',
            'save_address' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('addresses', [
            'user_id' => $this->buyer->id,
            'label' => 'Kantor',
            'address' => 'Jl. Kenanga No. 12, Bandung',
            'city' => 'Bandung',
            'is_default' => true,
        ]);
    }

    public function test_city_is_required_when_the_address_should_be_saved(): void
    {
        $this->actingAs($this->buyer)->post(route('books.cart.add', $this->book), ['quantity' => 1]);

        $this->actingAs($this->buyer)->post(route('checkout.store'), [
            'recipient_name' => 'Rina Kartika',
            'phone' => '081122334455',
            'address' => 'Jl. Kenanga No. 12',
            'payment_method' => 'transfer',
            'save_address' => '1',
        ])->assertSessionHasErrors('city');

        $this->assertDatabaseCount('addresses', 0);
    }

    public function test_checkout_without_the_save_checkbox_keeps_the_book_untouched(): void
    {
        $this->actingAs($this->buyer)->post(route('books.cart.add', $this->book), ['quantity' => 1]);

        $this->actingAs($this->buyer)->post(route('checkout.store'), [
            'recipient_name' => 'Rina Kartika',
            'phone' => '081122334455',
            'address' => 'Jl. Kenanga No. 12',
            'payment_method' => 'transfer',
        ])->assertRedirect();

        $this->assertDatabaseCount('addresses', 0);
    }
}
