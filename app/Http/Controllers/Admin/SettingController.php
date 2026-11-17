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
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'qris_image') {
                continue;
            }
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        if ($request->hasFile('qris_image')) {
            $oldQris = Setting::where('key', 'qris_image')->value('value');
            if ($oldQris && Storage::disk('public')->exists($oldQris)) {
                Storage::disk('public')->delete($oldQris);
            }
            $path = $request->file('qris_image')->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'qris_image'], ['value' => $path]);
        }

        return back()->with('success', 'Pengaturan website berhasil diperbarui.');
    }
}
