{{--
    Grid Component - Preline UI Compatible

    CSS Grid layout component using Tailwind Grid classes
    Based on: https://preline.co/docs/grid.html

    Safelist Tailwind pour classes dynamiques :
    Gaps: gap-0 gap-1 gap-2 gap-3 gap-4 gap-5 gap-6 gap-7 gap-8 gap-9 gap-10 gap-11 gap-12
--}}
@props([
    'cols' => null, // 1-12, none, subgrid
    'rows' => null, // 1-6, none, subgrid
    'gap' => null, // 0-96, x, y, px
    'flow' => null, // row, col, dense, row-dense, col-dense
    'autoRows' => null, // auto, min, max, fr
    'autoCols' => null, // auto, min, max, fr
    'class' => '', // Classes additionnelles
])

@php
    $baseClasses = ['grid'];

    // Gérer le nombre de colonnes
    if ($cols !== null) {
        $cols = is_array($cols) ? $cols : ['default' => $cols];

        $colClassList = [];

        foreach ($cols as $breakpoint => $colValue) {

            $colValue = (string)$colValue;

            $colClass = match ($colValue) {
                '1' => 'grid-cols-1',
                '2' => 'grid-cols-2',
                '3' => 'grid-cols-3',
                '4' => 'grid-cols-4',
                '5' => 'grid-cols-5',
                '6' => 'grid-cols-6',
                '7' => 'grid-cols-7',
                '8' => 'grid-cols-8',
                '9' => 'grid-cols-9',
                '10' => 'grid-cols-10',
                '11' => 'grid-cols-11',
                '12' => 'grid-cols-12',
                'none' => 'grid-cols-none',
                'subgrid' => 'grid-cols-subgrid',
                default => $colValue, // Allow custom values
            };

            if ($breakpoint === 'default') {
                $colClassList[] = $colClass;
            } else {
                $colClassList[] = "{$breakpoint}:{$colClass}";
            }
        }

        $baseClasses[] = implode(' ', $colClassList);
    }

    // Gérer le nombre de lignes
    if ($rows !== null) {
        $rows = is_array($rows) ? $cols : ['default' => $rows];

        $rowClassList = [];

        foreach ($rows as $breakpoint => $rowValue) {

            $rowValue = (string)$rowValue;

            $rowClass = match ($rowValue) {
                '1' => 'grid-rows-1',
                '2' => 'grid-rows-2',
                '3' => 'grid-rows-3',
                '4' => 'grid-rows-4',
                '5' => 'grid-rows-5',
                '6' => 'grid-rows-6',
                'none' => 'grid-rows-none',
                'subgrid' => 'grid-rows-subgrid',
                default => $rowValue, // Allow custom values
            };
            if ($breakpoint === 'default') {
                $rowClassList[] = $rowClass;
            } else {
                $rowClassList[] = "{$breakpoint}:{$rowClass}";
            }
        }

        $baseClasses[] = implode(' ', $rowClassList);
    }

    // Gérer le gap
    if ($gap !== null) {
        $gapClass = str_starts_with($gap, 'x-') || str_starts_with($gap, 'y-') ? "gap-{$gap}" : "gap-{$gap}";
        $baseClasses[] = $gapClass;
    }

    // Gérer le flow
    if ($flow !== null) {
        $flowClass = match ($flow) {
            'row' => 'grid-flow-row',
            'col' => 'grid-flow-col',
            'dense' => 'grid-flow-dense',
            'row-dense' => 'grid-flow-row-dense',
            'col-dense' => 'grid-flow-col-dense',
            default => $flow, // Allow custom values
        };
        $baseClasses[] = $flowClass;
    }

    // Gérer auto-rows
    if ($autoRows !== null) {
        $autoRowClass = match ($autoRows) {
            'auto' => 'auto-rows-auto',
            'min' => 'auto-rows-min',
            'max' => 'auto-rows-max',
            'fr' => 'auto-rows-fr',
            default => $autoRows,
        };
        $baseClasses[] = $autoRowClass;
    }

    // Gérer auto-cols
    if ($autoCols !== null) {
        $autoColClass = match ($autoCols) {
            'auto' => 'auto-cols-auto',
            'min' => 'auto-cols-min',
            'max' => 'auto-cols-max',
            'fr' => 'auto-cols-fr',
            default => $autoCols,
        };
        $baseClasses[] = $autoColClass;
    }

    $finalClasses = collect($baseClasses)->push($class)->filter()->implode(' ');
@endphp

<div {{ $attributes->twMerge($finalClasses) }}>
    {{ $slot }}
</div>
