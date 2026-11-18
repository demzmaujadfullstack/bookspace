<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminBrandingTest extends TestCase
{
    use RefreshDatabase;

    private const BRAND = 'Rak Baca Nusantara';

    /** @param  array<string, mixed>  $extra */
    private function payload(array $extra = []): array
    {
        return array_merge([
            'site_name' => self::BRAND,
            'site_tagline' => 'Toko Buku Online Terpercaya',
            'site_email' => 'info@bookspace.id',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'BOOKSPACE Indonesia',
            'carousel_duration' => 5000,
            'free_shipping_threshold' => 200000,
        ], $extra);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /** @return array{0: string, 1: string} paths of the stored logo and favicon */
    private function uploadBranding(User $admin): array
    {
        $this->actingAs($admin)->patch(route('admin.settings.update'), $this->payload([
            'logo_image' => UploadedFile::fake()->image('logo-toko.png', 320, 96),
            'favicon_image' => UploadedFile::fake()->image('ikon-tab.png', 64, 64),
            'logo_show_text' => '1',
        ]))->assertSessionHasNoErrors();

        return [Setting::value('logo_image'), Setting::value('favicon_image')];
    }

    public function test_admin_can_upload_logo_and_favicon(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        [$logo, $favicon] = $this->uploadBranding($admin);

        $this->assertNotNull($logo);
        $this->assertNotNull($favicon);
        Storage::disk('public')->assertExists($logo);
        Storage::disk('public')->assertExists($favicon);
    }

    public function test_uploaded_logo_and_favicon_appear_on_the_storefront(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        [$logo, $favicon] = $this->uploadBranding($admin);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('<img src="'.asset('storage/'.$logo).'?v=', $html);
        $this->assertStringContainsString('alt="Logo '.self::BRAND.'"', $html);
        $this->assertStringContainsString('<link rel="icon" href="'.asset('storage/'.$favicon).'?v=', $html);
        $this->assertStringContainsString('>'.self::BRAND.'<', $html);
    }

    public function test_branding_also_reaches_the_admin_and_auth_layouts(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        [, $favicon] = $this->uploadBranding($admin);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('/storage/'.$favicon, false);

        $this->post(route('logout'));

        $this->get(route('login'))->assertOk()->assertSee('/storage/'.$favicon, false);
    }

    public function test_wordmark_is_hidden_when_the_admin_turns_the_name_off(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->uploadBranding($admin);

        $this->actingAs($admin)->patch(route('admin.settings.update'), $this->payload([
            'logo_image' => UploadedFile::fake()->image('logo-toko.png', 320, 96),
        ]))->assertSessionHasNoErrors();

        $this->assertSame('0', Setting::value('logo_show_text'));

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('/storage/'.Setting::value('logo_image'), $html);
        $this->assertStringNotContainsString('>'.self::BRAND.'<', $html);
    }

    public function test_favicon_falls_back_to_the_logo(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->patch(route('admin.settings.update'), $this->payload([
            'logo_image' => UploadedFile::fake()->image('logo-toko.png', 320, 96),
            'logo_show_text' => '1',
        ]))->assertSessionHasNoErrors();

        $logo = Setting::value('logo_image');
        $this->assertNull(Setting::value('favicon_image'));
        $this->assertStringContainsString('/storage/'.$logo, $this->get('/')->getContent());
    }

    public function test_removing_the_logo_returns_to_the_builtin_mark(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        [$logo] = $this->uploadBranding($admin);

        $this->actingAs($admin)->patch(route('admin.settings.update'), $this->payload([
            'remove_logo_image' => '1',
            'logo_show_text' => '1',
        ]))->assertSessionHasNoErrors();

        $this->assertNull(Setting::value('logo_image'));
        Storage::disk('public')->assertMissing($logo);
        $this->assertStringNotContainsString('alt="Logo '.self::BRAND.'"', $this->get('/')->getContent());
    }

    public function test_uploading_a_new_logo_deletes_the_previous_file(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        [$logo] = $this->uploadBranding($admin);

        $this->actingAs($admin)->patch(route('admin.settings.update'), $this->payload([
            'logo_image' => UploadedFile::fake()->image('logo-baru.png', 320, 96),
            'logo_show_text' => '1',
        ]))->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing($logo);
        Storage::disk('public')->assertExists(Setting::value('logo_image'));
    }

    public function test_non_image_files_are_rejected_for_branding(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->patch(route('admin.settings.update'), $this->payload([
            'logo_image' => UploadedFile::fake()->create('logo-toko.txt', 12, 'text/plain'),
            'favicon_image' => UploadedFile::fake()->image('ikon-tab.png', 64, 64),
        ]))->assertSessionHasErrors(['logo_image' => 'logo toko harus berupa gambar.']);

        $this->assertNull(Setting::value('logo_image'));
        $this->assertNull(Setting::value('favicon_image'));
    }

    public function test_regular_reader_cannot_change_the_branding(): void
    {
        Storage::fake('public');
        $reader = User::factory()->create(['role' => 'user']);

        $this->actingAs($reader)
            ->patch(route('admin.settings.update'), $this->payload(['remove_logo_image' => '1']))
            ->assertForbidden();

        $this->assertNull(Setting::value('logo_image'));
    }
}
