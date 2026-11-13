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
            'footer_description' => ['nullable', 'string', 'max:1000'],
            'qris_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
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
