{{--
    Modal Component - Preline UI Compatible

    Modal component following Preline pattern
    Based on: https://preline.co/docs/modal.html

    Safelist Tailwind pour classes dynamiques :
    Sizes: max-w-xs max-w-sm max-w-md max-w-lg max-w-xl max-w-2xl max-w-full
    Themes: bg-cinema-dark bg-white border-cinema-gold border-gray-200
    Colors: text-cinema-gold text-gray-900 text-white
--}}
@props([
    // Modal Configuration
    'id' => 'modal',                    // Modal unique ID
    'theme' => 'admin',                 // admin | cinema
    'size' => 'md',                     // xs | sm | md | lg | xl | 2xl | full
    'type' => 'default',                // default | confirm | alert | success | warning | danger

    // Behavior
    'closable' => true,                 // Show close button
    'backdrop' => true,                 // Show backdrop overlay
    'backdropClose' => true,            // Close on backdrop click
    'escapeClose' => true,              // Close on ESC key
    'static' => false,                  // Static modal (no close interactions)
    'scrollable' => false,              // Make modal body scrollable
    'focus' => true,                    // Auto focus management

    // Content
    'title' => '',                      // Modal title
    'showHeader' => true,               // Show header section
    'showFooter' => false,              // Show footer section
    'icon' => null,                     // Header icon (for alert modals)

    // Animation
    'animation' => 'scale',             // scale | slide-up | slide-down | fade
    'duration' => '200',                // Animation duration in ms

    // Positioning
    'centered' => true,                 // Center modal vertically
    'fullscreen' => false,              // Fullscreen modal
    'position' => 'center',             // center | top | bottom

    // Accessibility
    'role' => 'dialog',                 // ARIA role
    'describedBy' => null,              // aria-describedby
    'live' => 'polite',                 // aria-live

    // Custom Classes
    'class' => '',                      // Additional classes
    'backdropClass' => '',              // Custom backdrop classes
    'contentClass' => '',               // Custom content classes
    'headerClass' => '',                // Custom header classes
    'bodyClass' => '',                  // Custom body classes
    'footerClass' => '',                // Custom footer classes
])

@php
    // === SIZE MAPPING ===
    $sizeMap = [
        'xs' => 'max-w-xs',
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        'full' => 'max-w-full mx-4',
    ];

    $sizeClasses = $sizeMap[$size] ?? $sizeMap['md'];

    // === THEME MAPPING ===
    $themeConfig = [
        'cinema' => [
            'backdrop' => 'bg-black/80',
            'modal' => 'bg-cinema-dark border-cinema-gold/30',
            'header' => 'border-b border-cinema-gold/20',
            'body' => 'text-cinema-text',
            'footer' => 'border-t border-cinema-gold/20',
            'close' => 'text-cinema-gold/60 hover:text-cinema-gold hover:bg-cinema-gold/10',
            'scrollbar' => '[&::-webkit-scrollbar-thumb]:bg-cinema-gold/30 [&::-webkit-scrollbar]:bg-cinema-dark',
        ],
        'admin' => [
            'backdrop' => 'bg-gray-900/50',
            'modal' => 'bg-white border border-gray-200',
            'header' => 'border-b border-gray-200 text-gray-900',
            'body' => 'text-gray-700',
            'footer' => 'border-t border-gray-200',
            'close' => 'bg-gray-100 text-gray-800 hover:bg-gray-200 focus:bg-gray-200',
            'scrollbar' => '[&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar]:bg-gray-100',
        ],
    ];

    $currentTheme = $themeConfig[$theme] ?? $themeConfig['admin'];

    // === TYPE COLOR MAPPING ===
    $typeConfig = [
        'success' => [
            'cinema' => [
                'modal' => 'border-green-400 bg-cinema-dark', // Bordure colorée, fond noir
                'header' => 'border-b border-green-400/20 text-green-400', // Titre coloré
                'close' => 'text-green-400/60 hover:text-green-400 hover:bg-green-400/10',
            ],
            'admin' => [
                'modal' => 'border-green-200 bg-green-50',
                'header' => 'border-b border-green-200 text-green-800',
                'close' => 'text-green-600 hover:text-green-800 hover:bg-green-100',
            ],
        ],
        'warning' => [
            'cinema' => [
                'modal' => 'border-yellow-400 bg-cinema-dark', // Bordure colorée, fond noir
                'header' => 'border-b border-yellow-400/20 text-yellow-400', // Titre coloré
                'close' => 'text-yellow-400/60 hover:text-yellow-400 hover:bg-yellow-400/10',
            ],
            'admin' => [
                'modal' => 'border-yellow-200 bg-yellow-50',
                'header' => 'border-b border-yellow-200 text-yellow-800',
                'close' => 'text-yellow-600 hover:text-yellow-800 hover:bg-yellow-100',
            ],
        ],
        'danger' => [
            'cinema' => [
                'modal' => 'border-cinema-red bg-cinema-dark', // Bordure rouge, fond noir
                'header' => 'border-b border-cinema-red/20 text-red-400', // Titre rouge vif
                'close' => 'text-cinema-red/60 hover:text-cinema-red hover:bg-cinema-red/10',
            ],
            'admin' => [
                'modal' => 'border-red-200 bg-red-50',
                'header' => 'border-b border-red-200 text-red-800',
                'close' => 'text-red-600 hover:text-red-800 hover:bg-red-100',
            ],
        ],
        'confirm' => [
            'cinema' => [
                'modal' => 'border-blue-400 bg-cinema-dark', // Bordure bleue, fond noir
                'header' => 'border-b border-blue-400/20 text-blue-400', // Titre bleu
                'close' => 'text-blue-400/60 hover:text-blue-400 hover:bg-blue-400/10',
            ],
            'admin' => [
                'modal' => 'border-blue-200 bg-blue-50',
                'header' => 'border-b border-blue-200 text-blue-800',
                'close' => 'text-blue-600 hover:text-blue-800 hover:bg-blue-100',
            ],
        ],
        'default' => [
            'cinema' => null,
            'admin' => null,
        ],
    ];

    $typeOverrides = $typeConfig[$type][$theme] ?? null;

    // === POSITION MAPPING ===
    $positionClasses = match($position) {
        'top' => 'items-start pt-16',
        'bottom' => 'items-end pb-16',
        'center' => $centered ? 'items-center' : 'items-start pt-16',
        default => 'items-center'
    };

    // === ANIMATION MAPPING ===
    $animationClasses = match($animation) {
        'scale' => 'hs-overlay-animation-target hs-overlay-open:scale-100 hs-overlay-open:opacity-100 scale-95 opacity-0 ease-in-out transition-all duration-' . $duration,
        'slide-up' => 'hs-overlay-animation-target hs-overlay-open:translate-y-0 hs-overlay-open:opacity-100 translate-y-8 opacity-0 ease-in-out transition-all duration-' . $duration,
        'slide-down' => 'hs-overlay-animation-target hs-overlay-open:translate-y-0 hs-overlay-open:opacity-100 -translate-y-8 opacity-0 ease-in-out transition-all duration-' . $duration,
        'fade' => 'hs-overlay-animation-target hs-overlay-open:opacity-100 opacity-0 ease-in-out transition-all duration-' . $duration,
        default => 'hs-overlay-animation-target hs-overlay-open:scale-100 hs-overlay-open:opacity-100 scale-95 opacity-0 ease-in-out transition-all duration-' . $duration
    };

    // === MODAL CLASSES (Preline UI Structure) ===
    $modalClasses = [
        'flex',
        'flex-col',
        $typeOverrides['modal'] ?? $currentTheme['modal'],
        'border',
        'shadow-2xs',
        'rounded-xl',
        'pointer-events-auto'
    ];

    if ($fullscreen) {
        $modalClasses = array_merge($modalClasses, ['h-full', 'max-w-none', 'rounded-none']);
    }

    // === BACKDROP CLASSES (Preline UI) ===
    $backdropClasses = [
        'hs-overlay',
        'hs-overlay-open:opacity-100',
        'hs-overlay-open:duration-500',
        'hidden',
        'size-full',
        'fixed',
        'top-0',
        'start-0',
        'z-[80]',
        'opacity-0',
        'overflow-x-hidden',
        'transition-all',
        'overflow-y-auto',
        'pointer-events-none',
        $currentTheme['backdrop']
    ];

    // Positioning classes for backdrop
    $backdropClasses = array_merge($backdropClasses, [$positionClasses]);

    // Auto-close behavior for Preline
    if (!$static) {
        if (!$backdropClose) {
            $backdropClasses[] = '[--auto-close:false]';
        }
        if (!$escapeClose) {
            $backdropClasses[] = '[--keyboard:false]';
        }
    } else {
        $backdropClasses[] = '[--static:true]';
        $backdropClasses[] = '[--keyboard:false]';
        $backdropClasses[] = '[--auto-close:false]';
    }

    $finalBackdropClasses = collect($backdropClasses)
        ->push($backdropClass)
        ->filter()
        ->implode(' ');

    $finalModalClasses = collect($modalClasses)
        ->push($contentClass)
        ->push($class)
        ->filter()
        ->implode(' ');
@endphp

{{-- Structure EXACTE Preline UI --}}
<div id="{{ $id }}"
     class="{{ $finalBackdropClasses }}"
     role="{{ $role }}"
     tabindex="-1"
     aria-labelledby="{{ $id }}-label"
     @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
     aria-live="{{ $live }}"
     aria-modal="true"
     @if($static)
     data-hs-overlay-keyboard="false"
     @endif
     @if(!$focus)
     data-hs-overlay-autofocus-prevention="true"
     @endif>

  <div class="{{ $animationClasses }} {{ $sizeClasses }} w-full m-3 mx-auto">
    <div class="{{ $finalModalClasses }}">

      @if($showHeader && ($title || $closable || isset($header) || $icon))
      <div class="flex justify-between items-center py-3 px-4 {{ $typeOverrides['header'] ?? $currentTheme['header'] }} {{ $headerClass }}">
        <div class="flex items-center gap-x-2">
          @if($icon)
          <div class="flex-shrink-0">
            @if(is_string($icon))
              <div class="{{ $icon }}"></div>
            @else
              {{ $icon }}
            @endif
          </div>
          @endif
          <h3 id="{{ $id }}-label" class="font-bold {{ $typeOverrides ? '' : ($theme === 'cinema' ? 'text-cinema-gold' : 'text-gray-800 dark:text-white') }}">
            {{ $title }}
            @isset($header){{ $header }}@endisset
          </h3>
        </div>

        @if($closable && !$static)
        <button type="button"
                class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent {{ $typeOverrides['close'] ?? $currentTheme['close'] }} focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                aria-label="Close"
                data-hs-overlay="#{{ $id }}">
          <span class="sr-only">Close</span>
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m18 6-12 12"></path>
            <path d="m6 6 12 12"></path>
          </svg>
        </button>
        @endif
      </div>
      @endif

      <div class="p-4 {{ $scrollable ? 'overflow-y-auto max-h-64' : '' }} {{ $currentTheme['body'] }} {{ $currentTheme['scrollbar'] }} {{ $bodyClass }}"
           @if($describedBy) aria-describedby="{{ $describedBy }}" @endif>
        {{ $slot }}
      </div>

      @if($showFooter && isset($footer))
      <div class="flex justify-end items-center gap-x-2 py-3 px-4 {{ $currentTheme['footer'] }} {{ $footerClass }}">
        {{ $footer }}
      </div>
      @endif

    </div>
  </div>
</div>