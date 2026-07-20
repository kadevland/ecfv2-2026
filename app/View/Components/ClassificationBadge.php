<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use ValueError;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Domain\Enums\ClassificationFilm;

class ClassificationBadge extends Component
{
    public readonly ?ClassificationFilm $classification;

    public readonly string $label;

    public readonly string $colorClass;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public readonly ?string $value = null,
        public readonly string $class = 'px-2 py-1 bg-gray-100 rounded text-xs font-medium'
    ) {
        if ($this->value) {
            try {
                $this->classification = ClassificationFilm::from($this->value);
                $this->label          = $this->classification->label();
                $this->colorClass     = $this->classification->getColorClass();
            } catch (ValueError) {
                $this->classification = null;
                $this->label          = $this->value;
                $this->colorClass     = 'text-gray-500';
            }
        } else {
            $this->classification = null;
            $this->label          = 'Non classé';
            $this->colorClass     = 'text-gray-500';
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.classification-badge');
    }
}
