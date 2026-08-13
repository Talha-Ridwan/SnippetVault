<?php

namespace App\Services;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    public function issueApiToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function revokeCurrentToken(User $user): void
    {
        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();

        $token?->delete();
    }
}
