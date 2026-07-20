@if($value)
    <span {{ $attributes->merge(['class' => $class . ' ' . $colorClass]) }}>
        {{ $label }}
    </span>
@endif