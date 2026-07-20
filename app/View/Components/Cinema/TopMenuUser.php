<?php

declare(strict_types=1);

namespace App\View\Components\Cinema;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class TopMenuUser extends Component
{
    public bool $isConnected = false;

    public string $fullname = '';

    public string $email = '';

    public string $role = '';

    public ?string $avatar = null;

    /**
     * @var array<int, array<string, mixed>>
     */
    public $menus = [];

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->isConnected = Auth::check();

        if ($this->isConnected) {
            $user = Auth::user();

            // Récupérer le profil utilisateur unifié
            $profile = $user->profil;

            // Construire le nom complet depuis le profil
            if ($profile) {
                $this->fullname = trim(($profile->prenom ?? '') . ' ' . ($profile->nom ?? ''));
                if (empty($this->fullname)) {
                    $this->fullname = $user->email ?? 'Utilisateur';
                }
            } else {
                $this->fullname = $user->email ?? 'Utilisateur';
            }

            $this->email = $user->email ?? '';
            $this->role  = $user->type?->value ?? 'client';

            // Menu selon le rôle utilisateur
            if ($user->type?->value === 'admin') {
                $this->menus = [
                    ['label' => 'Mon profil', 'link' => route('account'), 'icon' => 'heroicon-o-user-circle'],
                    ['label' => 'Mes réservations', 'link' => route('account.reservations'), 'icon' => 'heroicon-o-ticket'],
                    ['label' => 'Back Office', 'link' => route('admin.dashboard'), 'icon' => 'heroicon-o-cog-8-tooth'],
                ];
            } elseif ($user->type?->value === 'employee') {
                $this->menus = [
                    ['label' => 'Mon profil', 'link' => route('account'), 'icon' => 'heroicon-o-user-circle'],
                    ['label' => 'Mes réservations', 'link' => route('account.reservations'), 'icon' => 'heroicon-o-ticket'],
                    ['label' => 'Back Office', 'link' => route('employee.dashboard'), 'icon' => 'heroicon-o-cog-8-tooth'],
                ];
            } else {
                // Client
                $this->menus = [
                    ['label' => 'Mon profil', 'link' => route('account'), 'icon' => 'heroicon-o-user-circle'],
                    ['label' => 'Mes réservations', 'link' => route('account.reservations'), 'icon' => 'heroicon-o-ticket'],
                ];
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cinema.top-menu-user');
    }
}
