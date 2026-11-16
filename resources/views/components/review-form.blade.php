@props(['book', 'review' => null])

@php
    $currentRating = (int) old('rating', $review?->rating ?? 5);
    $currentComment = old('comment', $review?->comment);
    $action = $review
        ? route('books.reviews.update', [$book, $review])
        : route('books.reviews.store', $book);
@endphp

<div class="bg-[#fafaf8] border border-[#e8e6df] rounded-2xl p-6 shadow-sm" x-data="{
    rating: {{ $currentRating }},
    hoverRating: 0,
    get ratingLabel() {
        const labels = { 1: '1 Bintang — Kurang', 2: '2 Bintang — Cukup', 3: '3 Bintang — Bagus', 4: '4 Bintang — Sangat Bagus', 5: '5 Bintang — Luar Biasa (Rekomendasi)' };
        return labels[this.hoverRating || this.rating] || '';
    }
}">
    <h3 class="font-serif-display font-bold text-lg text-[#0f2255] mb-1">
        {{ $review ? 'Perbarui Ulasan Anda' : 'Tulis Ulasan &amp; Kesan Anda' }}
    </h3>
    <p class="text-xs text-gray-500 mb-5 font-serif italic">
        {{ $review ? 'Perubahan akan langsung terlihat oleh pembaca lain.' : 'Bagikan impresi dan pengalaman membaca Anda untuk membantu pembaca lain.' }}
    </p>

    <form method="POST" action="{{ $action }}" class="space-y-4">
        @csrf
        @if($review)
            @method('PUT')
        @endif

        {{-- Star Rating Picker --}}
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Rating Bintang</label>
            <div class="flex items-center gap-1.5">
                <template x-for="star in 5" :key="star">
                    <button
                        type="button"
                        @click="rating = star"
                        @mouseover="hoverRating = star"
                        @mouseleave="hoverRating = 0"
                        class="p-1 focus:outline-none transition-transform hover:scale-110"
                        :title="star + ' Bintang'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 transition-colors" viewBox="0 0 24 24" :fill="(hoverRating || rating) >= star ? '#f59e0b' : 'transparent'" :stroke="(hoverRating || rating) >= star ? '#f59e0b' : '#d1d5db'" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519 4.674z"/>
                        </svg>
                    </button>
                </template>
                <span class="text-xs font-semibold text-[#e05c1a] ml-2" x-text="ratingLabel"></span>
            </div>
            <input type="hidden" name="rating" :value="rating">
            @error('rating')
                <p class="text-[11px] text-red-600 mt-1.5 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        {{-- Comment Textarea --}}
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Ulasan Anda</label>
            <textarea
                name="comment"
                rows="3"
                placeholder="Tuliskan pengalaman Anda membaca buku ini, gaya bahasa penulis, alur cerita, atau pesan penting yang Anda dapatkan..."
                class="w-full text-xs sm:text-sm border border-[#e8e6df] rounded-xl p-3.5 focus:outline-none focus:border-[#0f2255] bg-white text-gray-800 placeholder-gray-400 font-serif"
                required
                minlength="5"
                maxlength="500"
            >{{ $currentComment }}</textarea>
            <p class="text-[10px] text-gray-400 mt-1">Minimal 5 karakter, maksimal 500 karakter.</p>
            @error('comment')
                <p class="text-[11px] text-red-600 mt-1.5 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary text-xs py-2.5 px-5">
                {{ $review ? 'Simpan Perubahan Ulasan' : 'Kirim Ulasan Pembaca' }}
            </button>
            @if($review)
                <button type="button" class="text-xs font-semibold text-gray-500 hover:text-gray-700" @click="$dispatch('cancel-review-edit')">
                    Batal
                </button>
            @endif
        </div>
    </form>
</div>
