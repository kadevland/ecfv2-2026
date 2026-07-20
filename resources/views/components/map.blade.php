@props([
    'latitude',
    'longitude',
    'title' => 'Localisation',
    'height' => '400px',
    'zoom' => 15,
    'id' => 'map-' . uniqid()
])

<div {{ $attributes->merge(['class' => 'relative']) }}>
    <div
        id="{{ $id }}"
        class="hs-leaflet w-full"
        style="height: {{ $height }};"
        data-latitude="{{ $latitude }}"
        data-longitude="{{ $longitude }}"
        data-zoom="{{ $zoom }}"
        data-title="{{ $title }}"
    >
        <!-- Carte sera initialisée par JavaScript -->
    </div>
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
              integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
              crossorigin="" />
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
                crossorigin=""></script>
    @endpush
@endonce