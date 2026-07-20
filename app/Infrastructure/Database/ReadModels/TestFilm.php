<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\ReadModels;

use MongoDB\Laravel\Eloquent\Model;

class TestFilm extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'films_catalogue';

    protected $guarded = [];
}
