{{--
    Columns Component - Preline UI Compatible

    Multi-column layout component using CSS Multi-Column Layout (Preline style)
    Based on: https://preline.co/docs/columns.html
--}}
@props([
    'count' => null,         // 1-12, auto, ou null pour width-based
    'width' => null,         // 3xs, 2xs, xs, sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl
    'gap' => '8',           // Espacement horizontal entre colonnes
    'spacing' => '8',       // Espacement vertical entre éléments
    'class' => '',          // Classes additionnelles
])

@php
    // Utiliser les vraies classes CSS Columns de Preline
    $baseClasses = [];

    // Gérer le nombre de colonnes
    if ($count !== null) {
        $columnClass = match($count) {
            'auto' => 'columns-auto',
            '1' => 'columns-1',
            '2' => 'columns-2',
            '3' => 'columns-3',
            '4' => 'columns-4',
            '5' => 'columns-5',
            '6' => 'columns-6',
            '7' => 'columns-7',
            '8' => 'columns-8',
            '9' => 'columns-9',
            '10' => 'columns-10',
            '11' => 'columns-11',
            '12' => 'columns-12',
            default => 'columns-auto',
        };
        $baseClasses[] = $columnClass;
    } else if ($width !== null) {
        // Gérer la largeur des colonnes (Preline classes)
        $widthClass = match($width) {
            '3xs' => 'columns-3xs',  // 16rem
            '2xs' => 'columns-2xs',  // 18rem
            'xs' => 'columns-xs',    // 20rem
            'sm' => 'columns-sm',    // 24rem
            'md' => 'columns-md',    // 28rem
            'lg' => 'columns-lg',    // 32rem
            'xl' => 'columns-xl',    // 36rem
            '2xl' => 'columns-2xl',  // 42rem
            '3xl' => 'columns-3xl',  // 48rem
            '4xl' => 'columns-4xl',  // 56rem
            '5xl' => 'columns-5xl',  // 64rem
            '6xl' => 'columns-6xl',  // 72rem
            '7xl' => 'columns-7xl',  // 80rem
            default => 'columns-auto',
        };
        $baseClasses[] = $widthClass;
    } else {
        $baseClasses[] = 'columns-auto';
    }

    // Ajouter l'espacement (gap-x pour colonnes CSS)
    $baseClasses[] = "gap-x-{$gap}";
    $baseClasses[] = "space-y-{$spacing}";

    $finalClasses = collect($baseClasses)
        ->push($class)
        ->filter()
        ->implode(' ');
@endphp

<div {{ $attributes->twMerge($finalClasses) }}>
    {{ $slot }}
</div>