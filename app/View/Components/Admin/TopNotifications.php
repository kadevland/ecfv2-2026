<?php

declare(strict_types=1);

namespace App\View\Components\Admin;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class TopNotifications extends Component
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public $notifications = [];

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->notifications = [

            // ['type' => 'success', 'message' => 'New user registered', 'time' => '2 min ago', 'avatar' => 'https://i.pravatar.cc/150?img=3'],
            // ['type' => 'info', 'message' => 'Server rebooted', 'time' => '10 min ago'],
            // ['type' => 'warning', 'message' => 'High memory usage', 'time' => '30 min ago'],
            // ['type' => 'danger', 'message' => 'New order received', 'time' => '1 hr ago'],

        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.top-notifications');
    }
}
