<?php

declare(strict_types=1);

namespace App\Application\Reviews\Listeners;

use DateTime;
use Illuminate\Support\Facades\Log;
use App\Domain\Reviews\Events\ReviewLiked;
use App\Domain\Reviews\Events\ReviewCreated;
use App\Domain\Reviews\Events\ReviewSignaled;
use App\Domain\Reviews\Events\ReviewModerated;

final class SyncReviewsToMongoDb
{
    public function handleReviewCreated(ReviewCreated $event): void
    {

        Log::info('Review created event handled', [
            'event'     => 'ReviewCreated',
            'timestamp' => (new DateTime)->format('Y-m-d H:i:s'),
        ]);
    }

    public function handleReviewModerated(ReviewModerated $event): void
    {

        Log::info('Review moderated event handled', [
            'event'     => 'ReviewModerated',
            'timestamp' => (new DateTime)->format('Y-m-d H:i:s'),
        ]);
    }

    public function handleReviewLiked(ReviewLiked $event): void
    {

        Log::info('Review liked event handled', [
            'event'     => 'ReviewLiked',
            'timestamp' => (new DateTime)->format('Y-m-d H:i:s'),
        ]);
    }

    public function handleReviewSignaled(ReviewSignaled $event): void
    {

        Log::info('Review signaled event handled', [
            'event'     => 'ReviewSignaled',
            'timestamp' => (new DateTime)->format('Y-m-d H:i:s'),
        ]);
    }
}
