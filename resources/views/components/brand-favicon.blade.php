@php
    $favicon = \App\Models\Setting::faviconUrl();
    $custom = \App\Models\Setting::mediaUrl('favicon_image') ?? \App\Models\Setting::logoUrl();
@endphp

<link rel="icon" href="{{ $favicon }}">
@if($custom)
    <link rel="apple-touch-icon" href="{{ $custom }}">
@endif
