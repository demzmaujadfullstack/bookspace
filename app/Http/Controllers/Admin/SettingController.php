<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    /** @var list<string> */
    private const IMAGE_FIELDS = ['qris_image', 'logo_image', 'favicon_image'];

    public function edit(): View
    {
        $settings = Setting::values();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'site_tagline' => ['nullable', 'string', 'max:200'],
            'site_email' => ['required', 'email', 'max:150'],
            'site_phone' => ['nullable', 'string', 'max:30'],
            'site_whatsapp' => ['nullable', 'string', 'max:30'],
            'site_address' => ['nullable', 'string', 'max:500'],
            'bank_name' => ['required', 'string', 'max:80'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'bank_account_name' => ['required', 'string', 'max:120'],
            'carousel_duration' => ['required', 'integer', 'min:1000', 'max:30000'],
            'free_shipping_threshold' => ['required', 'integer', 'min:0', 'max:10000000'],
            'footer_description' => ['nullable', 'string', 'max:1000'],
            'qris_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'logo_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'favicon_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
            'remove_logo_image' => ['nullable', 'boolean'],
            'remove_favicon_image' => ['nullable', 'boolean'],
        ], [
            'required' => ':attribute wajib diisi.',
            'email' => 'Format :attribute tidak sesuai.',
            'max' => [
                'string' => ':attribute maksimal :max karakter.',
                'numeric' => ':attribute melebihi batas nilai :max.',
                'file' => 'Ukuran :attribute maksimal :max kilobyte.',
            ],
            'min' => [
                'string' => ':attribute minimal :min karakter.',
                'numeric' => ':attribute belum mencapai nilai minimal :min.',
            ],
            'integer' => ':attribute harus berupa angka tanpa koma.',
            'image' => ':attribute harus berupa gambar.',
            'mimes' => ':attribute harus berformat JPEG, PNG, JPG, atau WEBP.',
            'carousel_duration.min' => 'Durasi carousel minimal 1000 milidetik (1 detik).',
            'carousel_duration.max' => 'Durasi carousel maksimal 30000 milidetik (30 detik).',
            'free_shipping_threshold.max' => 'Ambang gratis ongkir maksimal Rp10.000.000.',
            'qris_image.max' => 'Gambar QRIS maksimal 3 MB.',
            'logo_image.max' => 'Ukuran logo maksimal 2 MB.',
            'favicon_image.max' => 'Ukuran favicon maksimal 1 MB.',
        ], [
            'site_name' => 'nama toko',
            'site_tagline' => 'tagline',
            'site_email' => 'email toko',
            'site_phone' => 'nomor telepon toko',
            'site_whatsapp' => 'nomor WhatsApp toko',
            'site_address' => 'alamat toko',
            'bank_name' => 'nama bank',
            'bank_account_number' => 'nomor rekening',
            'bank_account_name' => 'nama pemilik rekening',
            'carousel_duration' => 'durasi carousel',
            'free_shipping_threshold' => 'ambang gratis ongkir',
            'footer_description' => 'deskripsi footer',
            'qris_image' => 'gambar QRIS',
            'logo_image' => 'logo toko',
            'favicon_image' => 'favicon',
        ]);

        foreach ($validated as $key => $value) {
            if (in_array($key, self::IMAGE_FIELDS, true) || str_starts_with($key, 'remove_')) {
                continue;
            }
            Setting::put($key, $value);
        }

        Setting::put('logo_show_text', $request->boolean('logo_show_text') ? '1' : '0');

        foreach (['qris_image', 'logo_image', 'favicon_image'] as $field) {
            if ($request->boolean('remove_'.$field)) {
                $this->forgetImage($field);
            } elseif ($request->hasFile($field)) {
                $this->forgetImage($field);
                Setting::put($field, $request->file($field)->store('settings', 'public'));
            }
        }

        return back()->with('success', 'Pengaturan website berhasil diperbarui.');
    }

    private function forgetImage(string $field): void
    {
        $path = Setting::where('key', $field)->value('value');

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        Setting::where('key', $field)->get()->each->delete();
    }
}
