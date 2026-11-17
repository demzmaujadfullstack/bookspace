<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $this->validateAddress($request);
        $validated['is_default'] = $request->boolean('is_default') || $user->addresses()->count() === 0;

        $address = $user->addresses()->create($validated);
        $this->makeExclusiveDefault($user, $address);

        return back()->with('success', 'Alamat pengiriman berhasil disimpan.');
    }

    public function update(Request $request, Address $address): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->authorizeAddress($request, $address);

        $validated = $this->validateAddress($request);
        $validated['is_default'] = $request->boolean('is_default');

        $address->update($validated);
        $this->makeExclusiveDefault($user, $address);

        return back()->with('success', 'Alamat pengiriman berhasil diperbarui.');
    }

    public function makeDefault(Request $request, Address $address): RedirectResponse
    {
        $this->authorizeAddress($request, $address);

        $address->update(['is_default' => true]);
        $this->makeExclusiveDefault($request->user(), $address);

        $label = $address->label ?: 'Tersimpan';

        return back()->with('success', "Alamat \"{$label}\" sekarang jadi alamat utama.");
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        $this->authorizeAddress($request, $address);

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $request->user()->addresses()->oldest('id')->first()?->update(['is_default' => true]);
        }

        return back()->with('success', 'Alamat pengiriman berhasil dihapus.');
    }

    private function authorizeAddress(Request $request, Address $address): void
    {
        abort_unless($address->user_id === $request->user()->id, 403, 'Alamat ini bukan milik Anda.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:60'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:80'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'is_default' => ['nullable', 'boolean'],
        ], [
            'recipient_name.required' => 'Nama penerima wajib diisi.',
            'phone.required' => 'Nomor WhatsApp / telepon penerima wajib diisi.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'city.required' => 'Kota atau kabupaten wajib diisi.',
        ], [
            'label' => 'label alamat',
            'recipient_name' => 'nama penerima',
            'phone' => 'nomor telepon',
            'address' => 'alamat',
            'city' => 'kota',
            'postal_code' => 'kode pos',
        ]);
    }

    private function makeExclusiveDefault(User $user, Address $address): void
    {
        if (! $address->is_default) {
            return;
        }

        $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
    }
}
