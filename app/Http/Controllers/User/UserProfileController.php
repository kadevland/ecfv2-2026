<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\User\Services\UserProfileService;

final class UserProfileController extends Controller
{
    public function __construct(
        private readonly UserProfileService $userProfileService
    ) {}

    /**
     * Show dashboard for authenticated users
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        return view('dashboard', [
            'user'    => $user,
            'profile' => $this->userProfileService->getUserProfile($user),
        ]);
    }

    /**
     * Get authenticated user profile (API)
     */
    public function profile(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'user' => [
                'uuid'      => $user->uuid,
                'type'      => $user->type->value,
                'is_active' => $user->is_active,
                'email'     => $user->credential->email,
                'profile'   => $this->userProfileService->getUserProfileForApi($user),
            ],
        ]);
    }
}
