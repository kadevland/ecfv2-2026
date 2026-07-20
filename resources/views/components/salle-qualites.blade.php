@props(['qualiteProjection' => [], 'qualiteSonore' => []])

@php
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Domain\Cinema\Enums\QualiteSonore;
@endphp

<!-- Qualités Projection et Sonore -->
@if(!empty($qualiteProjection) || !empty($qualiteSonore))
<div class="flex flex-wrap gap-2 mb-3">
    {{-- Qualités de projection --}}
    @if(!empty($qualiteProjection))
        @foreach($qualiteProjection as $tech)
            @php
            try {
                $qualiteEnum = QualiteProjection::from($tech);
                $label = $qualiteEnum->getLabel();
            } catch (ValueError) {
                $label = $tech; // Fallback si l'enum n'existe pas
            }
            @endphp
            <span class="bg-gold/20 text-gold px-2 py-1 rounded text-xs font-medium">
                {{ $label }}
            </span>
        @endforeach
    @endif

    {{-- Qualités sonores --}}
    @if(!empty($qualiteSonore))
        @foreach($qualiteSonore as $tech)
            @php
            try {
                $qualiteEnum = QualiteSonore::from($tech);
                $label = $qualiteEnum->getLabel();
            } catch (ValueError) {
                $label = $tech; // Fallback si l'enum n'existe pas
            }
            @endphp
            <span class="bg-blue-400/20 text-blue-400 px-2 py-1 rounded text-xs font-medium">
                {{ $label }}
            </span>
        @endforeach
    @endif
</div>
@endif