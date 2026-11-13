<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/akun/profil');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/akun/profil', [
                'name' => 'Dimas Baru',
                'email' => 'dimasbaru@example.com',
                'phone' => '08123456789',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $user->refresh();

        $this->assertSame('Dimas Baru', $user->name);
        $this->assertSame('dimasbaru@example.com', $user->email);
        $this->assertSame('08123456789', $user->phone);
    }
}
