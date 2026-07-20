<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Film;

use Illuminate\View\View;
use App\Http\Controllers\Controller;

final class CreateFilmController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.films.create');
    }
}
