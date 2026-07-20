<?php

declare(strict_types=1);

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Sidebar extends Component
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public array $menus = [];

    protected string $currentRouteName;

    protected string $currentUrl;

    /**
     * Create a new component instance.
     */
    public function __construct(Request $request)
    {

        $this->currentRouteName = $request->route()
            ->getName();
        $this->currentUrl = $request->url();

        $this->menus = [

            $this->MenuDahsboard(),
            $this->MenuCinemas(),
            $this->MenuSalles(),
            $this->MenuFilms(),
            $this->MenuSeances(),
            $this->MenuReservations(),
            $this->MenuClients(),
            $this->MenuPersonnels(),
            // $this->MenuIncidents(),
            ['isDivider' => true],
            // $this->Statistiques(),
            // $this->Paramètres(),

        ];

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.sidebar.index');
    }

    protected function isActiveLink(string $link): bool
    {
        return $this->currentRouteName === $link || $this->currentUrl === $link;
    }

    protected function isGroupActive(string $link): bool
    {
        return str_starts_with($this->currentRouteName, $link) || str_starts_with($this->currentUrl, $link);
    }

    /**
     * @return array{title: string, icon: string, link: string, isActive: bool}
     */
    protected function MenuDahsboard(): array
    {

        return ['title' => 'Dashboard', 'icon' => 'iconoir-dashboard-dots', 'link' => route('admin.dashboard'), 'isActive' => $this->isActiveLink('dashboard')];
    }

    /**
     * @return array{title: string, icon: string, link: string, isActive: bool, isOpen: bool, subMenus: array<int, array{title: string, icon: string, link: string, isActive: bool}>}
     */
    protected function MenuCinemas(): array
    {
        return [
            'title'    => 'Cinémas',
            'icon'     => 'bi-building',
            'link'     => '#',
            'isActive' => $this->isGroupActive('admin.cinemas'),
            'isOpen'   => $this->isGroupActive('admin.cinemas'),
            'subMenus' => [
                ['title' => 'Liste des cinémas', 'icon' => 'bi-list', 'link' => route('admin.cinemas.index'), 'isActive' => $this->isActiveLink('admin.cinemas.index')],
                ['title' => 'Ajouter un cinéma', 'icon' => 'bi-plus', 'link' => route('admin.cinemas.create'), 'isActive' => $this->isActiveLink('admin.cinemas.create')],
            ],
        ];

    }

    /**
     * @return array{title: string, icon: string, link: string, isActive: bool, isOpen: bool, subMenus: array<int, array{title: string, icon: string, link: string, isActive: bool}>}
     */
    protected function MenuSalles(): array
    {
        return [
            'title'    => 'Salles',
            'icon'     => 'heroicon-o-cube',
            'link'     => '#',
            'isActive' => $this->isGroupActive('admin.salles'),
            'isOpen'   => $this->isGroupActive('admin.salles'),
            'subMenus' => [
                ['title' => 'Liste des salles', 'icon' => 'bi-list', 'link' => route('admin.salles.index'), 'isActive' => $this->isActiveLink('admin.salles.index')],
                ['title' => 'Ajouter une salle', 'icon' => 'bi-plus', 'link' => route('admin.salles.create'), 'isActive' => $this->isActiveLink('admin.salles.create')],
            ],
        ];

    }

    /**
     * @return array{title: string, icon: string, link: string, isActive: bool, isOpen: bool, subMenus: array<int, array{title: string, icon: string, link: string, isActive: bool}>}
     */
    protected function MenuFilms(): array
    {
        return [
            'title'    => 'Films',
            'icon'     => 'iconoir-cinema-old',
            'link'     => '#',
            'isActive' => $this->isGroupActive('admin.films'),
            'isOpen'   => $this->isGroupActive('admin.films'),
            'subMenus' => [
                ['title' => 'Liste des Films', 'icon' => 'bi-list', 'link' => route('admin.films.index'), 'isActive' => $this->isActiveLink('admin.films.index')],
                ['title' => 'Ajouter un Film', 'icon' => 'bi-plus', 'link' => route('admin.films.create'), 'isActive' => $this->isActiveLink('admin.films.create')],
            ],
        ];

    }

    /**
     * @return array{title: string, icon: string, link: string, isActive: bool, isOpen: bool, subMenus: array<int, array{title: string, icon: string, link: string, isActive: bool}>}
     */
    protected function MenuSeances(): array
    {
        return [
            'title'    => 'Séance',
            'icon'     => 'heroicon-s-video-camera',
            'link'     => '#',
            'isActive' => $this->isGroupActive('admin.seances'),
            'isOpen'   => $this->isGroupActive('admin.seances'),
            'subMenus' => [
                ['title' => 'Liste des séances', 'icon' => 'bi-list', 'link' => route('admin.seances.index'), 'isActive' => $this->isActiveLink('admin.seances.index')],
                //['title' => 'Ajouter une séance', 'icon' => 'bi-plus', 'link' => route('admin.seances.create'), 'isActive' => $this->isActiveLink('admin.seances.create')],
            ],
        ];

    }

    /**
     * @return array{title: string, icon: string, link: string, isActive: bool, isOpen: bool, subMenus: array<int, array{title: string, icon: string, link: string, isActive: bool}>}
     */
    protected function MenuReservations(): array
    {
        return [
            'title'    => 'Réservations',
            'icon'     => 'bi-ticket',
            'link'     => '#',
            'isActive' => $this->isGroupActive('admin.reservations'),
            'isOpen'   => $this->isGroupActive('admin.reservations'),
            'subMenus' => [
                ['title' => 'Liste des réservations', 'icon' => 'bi-list', 'link' => route('admin.reservations.index'), 'isActive' => $this->isActiveLink('admin.reservations.index')],
            ],
        ];

    }

    /**
     * @return array{title: string, icon: string, link: string, isActive: bool, isOpen: bool, subMenus: array<int, array{title: string, icon: string, link: string, isActive: bool}>}
     */
    protected function MenuClients(): array
    {
        return [
            'title'    => 'Clients',
            'icon'     => 'heroicon-o-user',
            'link'     => '#',
            'isActive' => $this->isGroupActive('admin.users.clients'),
            'isOpen'   => $this->isGroupActive('admin.users.clients'),
            'subMenus' => [
                ['title' => 'Liste des clients', 'icon' => 'bi-list', 'link' => route('admin.users.clients.index'), 'isActive' => $this->isActiveLink('admin.users.clients.index')],
                //['title' => 'Ajouter un client', 'icon' => 'bi-plus', 'link' => '#', 'isActive' => $this->isActiveLink('admin.users.clients.create')],
            ],
        ];

    }

    /**
     * @return array{title: string, icon: string, link: string, isActive: bool, isOpen: bool, subMenus: array<int, array{title: string, icon: string, link: string, isActive: bool}>}
     */
    protected function MenuPersonnels(): array
    {
        return [
            'title'    => 'Employés',
            'icon'     => 'lineawesome-users-solid',
            'link'     => '#',
            'isActive' => $this->isGroupActive('admin.users.employees'),
            'isOpen'   => $this->isGroupActive('admin.users.employees'),
            'subMenus' => [
                ['title' => 'Liste des employés', 'icon' => 'bi-list', 'link' => route('admin.users.employees.index'), 'isActive' => $this->isActiveLink('admin.users.employees.index')],
                //['title' => 'Ajouter un employé', 'icon' => 'bi-plus', 'link' => '#', 'isActive' => $this->isActiveLink('admin.users.employees.create')],
            ],
        ];

    }
}
