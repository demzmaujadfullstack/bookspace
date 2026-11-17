<form method="GET" action="{{ route('books.index') }}" class="space-y-5">
    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

    <div class="flex items-center justify-between border-b border-[#f0eee6] pb-3">
        <h3 class="text-[13px] font-bold uppercase tracking-[0.12em] text-[#0f2255]">Saring</h3>
        <a href="{{ route('books.index') }}" class="text-[12px] font-semibold text-[#e05c1a] hover:underline">Atur ulang</a>
    </div>

    <div>
        <p class="eyebrow mb-2">Kategori</p>
        <div class="max-h-64 space-y-0.5 overflow-y-auto pr-1">
            <label class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2 py-1.5 text-[13px] hover:bg-[#fafaf8] {{ !request('category') ? 'font-bold text-[#0f2255]' : 'text-[#3f4451]' }}">
                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()" class="border-[#dcd8cd] text-[#0f2255] focus:ring-[#0f2255]/30">
                <span>Semua Kategori</span>
            </label>
            @foreach($categories as $cat)
                <label class="flex cursor-pointer items-center justify-between gap-2 rounded-lg px-2 py-1.5 text-[13px] hover:bg-[#fafaf8] {{ request('category') === $cat->slug ? 'font-bold text-[#0f2255]' : 'text-[#3f4451]' }}">
                    <span class="flex min-w-0 items-center gap-2.5">
                        <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'checked' : '' }} onchange="this.form.submit()" class="border-[#dcd8cd] text-[#0f2255] focus:ring-[#0f2255]/30">
                        <span class="truncate">{{ $cat->name }}</span>
                    </span>
                    <span class="shrink-0 text-[11px] text-[#9a9484]">{{ $cat->books_count }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="border-t border-[#f0eee6] pt-4">
        <p class="eyebrow mb-2">Rentang Harga (Rp)</p>
        <div class="flex items-center gap-2">
            <input type="number" name="min_price" min="0" step="1000" placeholder="Min" value="{{ request('min_price') }}" class="field !py-2 text-[13px]">
            <span class="text-[#9a9484]">&ndash;</span>
            <input type="number" name="max_price" min="0" step="1000" placeholder="Max" value="{{ request('max_price') }}" class="field !py-2 text-[13px]">
        </div>
    </div>

    <div class="border-t border-[#f0eee6] pt-4">
        <label class="flex cursor-pointer items-center gap-2.5 text-[13px] text-[#3f4451]">
            <input type="checkbox" name="promo" value="1" {{ request('promo') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-[#dcd8cd] text-[#e05c1a] focus:ring-[#e05c1a]/30">
            <span class="font-semibold">Hanya buku berdiskon</span>
        </label>
    </div>

    <button type="submit" class="btn-primary h-10 w-full rounded-lg text-[13px] font-semibold">Terapkan Filter</button>
</form>
