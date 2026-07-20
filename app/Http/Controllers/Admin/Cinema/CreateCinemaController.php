<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Cinema;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Application\Cinema\DTOs\CinemaFormDto;

final class CreateCinemaController extends Controller
{
    public function __invoke(): View
    {
        $cinemaForm = CinemaFormDto::empty();

        return view('admin.cinemas.create', [
            'cinema' => $cinemaForm,
        ]);
    }
}
