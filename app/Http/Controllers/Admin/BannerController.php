<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        $banners = Banner::orderBy('sort_order')->paginate(10);

        return view('admin.banners.index', compact('banners'));
    }

    public function create(): View
    {
        return view('admin.banners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:200'],
            'cta_text' => ['nullable', 'string', 'max:60'],
            'cta_link' => ['nullable', 'string', 'max:200'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = 'banners/default.jpg';
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');
        }

        Banner::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'cta_text' => $validated['cta_text'] ?? null,
            'cta_link' => $validated['cta_link'] ?? null,
            'sort_order' => $validated['sort_order'],
            'image' => $imagePath,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner carousel berhasil ditambahkan.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:200'],
            'cta_text' => ['nullable', 'string', 'max:60'],
            'cta_link' => ['nullable', 'string', 'max:200'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $banner->image = $request->file('image')->store('banners', 'public');
        }

        $banner->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'cta_text' => $validated['cta_text'] ?? null,
            'cta_link' => $validated['cta_link'] ?? null,
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner carousel berhasil diperbarui.');
    }

    public function toggle(Banner $banner): RedirectResponse
    {
        $banner->update(['is_active' => ! $banner->is_active]);

        $status = $banner->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Banner '{$banner->title}' berhasil {$status}.");
    }

    public function updateOrder(Request $request, Banner $banner): RedirectResponse
    {
        $request->validate([
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $banner->update(['sort_order' => $request->sort_order]);

        return back()->with('success', "Urutan banner '{$banner->title}' berhasil diperbarui.");
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $title = $banner->title;
        $banner->delete();

        return back()->with('success', "Banner '{$title}' berhasil dihapus.");
    }
}
