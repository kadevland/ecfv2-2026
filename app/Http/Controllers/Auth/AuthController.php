<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Validation\ValidationException;
use App\Domain\User\Services\UserProfileService;
use App\Infrastructure\Database\Models\Auth\User;

final class AuthController extends Controller
{
    public function __construct(
        private readonly UserProfileService $userProfileService
    ) {}

    /**
     * Login user and create access token
     */
    public function login(LoginRequest $request): JsonResponse
    {

        // Find user by credential email
        $user = User::whereHas('credential', function ($query) use ($request) {
            $query->where('email', $request->email);
        })->first();

        if (!$user || !Hash::check($request->password, $user->credential->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        // Check if user is active
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Votre compte est désactivé.'],
            ]);
        }

        // Create token
        $token = $user->createToken('auth-token', [
            'user:read',
            'user:update',
        ]);

        return response()->json([
            'access_token' => $token->plainTextToken,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'        => $user->id,
                'type'      => $user->type->value,
                'is_active' => $user->is_active,
                'email'     => $user->credential->email,
                'profile'   => $this->userProfileService->getUserProfileForApi($user),
            ],
        ]);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie',
        ]);
    }

    /**
     * Get authenticated user info
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

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
