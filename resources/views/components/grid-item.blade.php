{{--
    Grid Item Component - Preline UI Compatible

    Grid item component for positioning and spanning within grids
    Based on: https://preline.co/docs/grid.html
--}}
@props([
    'colSpan' => null,       // 1-12, auto, full
    'colStart' => null,      // 1-13, auto
    'colEnd' => null,        // 1-13, auto
    'rowSpan' => null,       // 1-6, auto, full
    'rowStart' => null,      // 1-7, auto
    'rowEnd' => null,        // 1-7, auto
    'class' => '',           // Classes additionnelles
])

@php
    $baseClasses = [];

    // Gérer col-span
    if ($colSpan !== null) {
        $colSpanClass = match($colSpan) {
            '1' => 'col-span-1',
            '2' => 'col-span-2',
            '3' => 'col-span-3',
            '4' => 'col-span-4',
            '5' => 'col-span-5',
            '6' => 'col-span-6',
            '7' => 'col-span-7',
            '8' => 'col-span-8',
            '9' => 'col-span-9',
            '10' => 'col-span-10',
            '11' => 'col-span-11',
            '12' => 'col-span-12',
            'auto' => 'col-auto',
            'full' => 'col-span-full',
            default => $colSpan,
        };
        $baseClasses[] = $colSpanClass;
    }

    // Gérer col-start
    if ($colStart !== null) {
        $colStartClass = match($colStart) {
            '1' => 'col-start-1',
            '2' => 'col-start-2',
            '3' => 'col-start-3',
            '4' => 'col-start-4',
            '5' => 'col-start-5',
            '6' => 'col-start-6',
            '7' => 'col-start-7',
            '8' => 'col-start-8',
            '9' => 'col-start-9',
            '10' => 'col-start-10',
            '11' => 'col-start-11',
            '12' => 'col-start-12',
            '13' => 'col-start-13',
            'auto' => 'col-start-auto',
            default => $colStart,
        };
        $baseClasses[] = $colStartClass;
    }

    // Gérer col-end
    if ($colEnd !== null) {
        $colEndClass = match($colEnd) {
            '1' => 'col-end-1',
            '2' => 'col-end-2',
            '3' => 'col-end-3',
            '4' => 'col-end-4',
            '5' => 'col-end-5',
            '6' => 'col-end-6',
            '7' => 'col-end-7',
            '8' => 'col-end-8',
            '9' => 'col-end-9',
            '10' => 'col-end-10',
            '11' => 'col-end-11',
            '12' => 'col-end-12',
            '13' => 'col-end-13',
            'auto' => 'col-end-auto',
            default => $colEnd,
        };
        $baseClasses[] = $colEndClass;
    }

    // Gérer row-span
    if ($rowSpan !== null) {
        $rowSpanClass = match($rowSpan) {
            '1' => 'row-span-1',
            '2' => 'row-span-2',
            '3' => 'row-span-3',
            '4' => 'row-span-4',
            '5' => 'row-span-5',
            '6' => 'row-span-6',
            'auto' => 'row-auto',
            'full' => 'row-span-full',
            default => $rowSpan,
        };
        $baseClasses[] = $rowSpanClass;
    }

    // Gérer row-start
    if ($rowStart !== null) {
        $rowStartClass = match($rowStart) {
            '1' => 'row-start-1',
            '2' => 'row-start-2',
            '3' => 'row-start-3',
            '4' => 'row-start-4',
            '5' => 'row-start-5',
            '6' => 'row-start-6',
            '7' => 'row-start-7',
            'auto' => 'row-start-auto',
            default => $rowStart,
        };
        $baseClasses[] = $rowStartClass;
    }

    // Gérer row-end
    if ($rowEnd !== null) {
        $rowEndClass = match($rowEnd) {
            '1' => 'row-end-1',
            '2' => 'row-end-2',
            '3' => 'row-end-3',
            '4' => 'row-end-4',
            '5' => 'row-end-5',
            '6' => 'row-end-6',
            '7' => 'row-end-7',
            'auto' => 'row-end-auto',
            default => $rowEnd,
        };
        $baseClasses[] = $rowEndClass;
    }

    $finalClasses = collect($baseClasses)
        ->push($class)
        ->filter()
        ->implode(' ');
@endphp

<div {{ $attributes->twMerge($finalClasses) }}>
    {{ $slot }}
</div>