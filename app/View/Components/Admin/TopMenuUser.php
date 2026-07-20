<?php

declare(strict_types=1);

namespace App\View\Components\Admin;

use Closure;
use App\Infrastructure\Database\Models\Auth\UserCredential;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class TopMenuUser extends Component
{
    public string $fullname;

    public string $email;

    public string $role;

    public ?string $avatar = null;

    /**
     * @var array<int, array<string, mixed>>
     */
    public $menus = [];

    /**
     * Create a new component instance.
     */
    public function __construct ()
    {

        $user       = Auth::user();
        $profil     = \App\Infrastructure\Database\Models\Profiles\UserProfil::where('user_uuid', $user->id)->first();
        $credential = UserCredential::where('user_uuid', $user->id)->first();

        $email = $credential?->email ?? '';
        $gravatarHash = md5(strtolower(trim($email ?? '')));
        $gravatarUrl  = "https://www.gravatar.com/avatar/{$gravatarHash}?s=32&d=identicon&r=pg";


        $this->fullname = $profil?->FullName ?? '';
        $this->email    = $email;
        $this->role     = 'Administrateur';
        $this->avatar   = $gravatarUrl;

        //$this->avatarUrl = $gravatarUrl;
        $this->menus     = [

            ['label' => ' Mon profil', 'link' => '#', 'icon' => 'heroicon-o-user-circle'],
            ['label' => ' Paramètres', 'link' => '#', 'icon' => 'heroicon-o-cog-8-tooth'],
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render () : View|Closure|string
    {
        return view('components.admin.top-menu-user');
    }
}
