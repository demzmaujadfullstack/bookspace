@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'bg-red-50 border border-red-200 text-red-800 rounded-2xl p-4 text-xs']) }} role="alert">
        <p class="font-bold mb-1.5">Periksa kembali isian berikut:</p>
        <ul class="list-disc pl-5 space-y-0.5">
            @foreach ($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
