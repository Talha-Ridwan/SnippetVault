<?php

namespace App\Services;

use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GithubAuthService
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('github')->stateless()->redirect();
    }

    public function userFromCallback(): SocialiteUser
    {
        return Socialite::driver('github')->stateless()->user();
    }

    public function resolveUser(SocialiteUser $githubUser): User
    {
        // Check by GitHub ID
        $user = User::query()->where('github_id', $githubUser->id)->first();

        if ($user) {
            return $user;
        }

        $user = User::query()->where('email', $githubUser->email)->first();
        // Handling a rare case where the user might change their concerned profile attributes
        if ($user) {
            $user->update([
                'github_id' => $githubUser->id,
                'avatar' => $githubUser->avatar,
            ]);

            return $user;
        }

        // Create New User
        return User::query()->create([
            'name' => $githubUser->name ?? $githubUser->nickname,
            'email' => $githubUser->email,
            'github_id' => $githubUser->id,
            'avatar' => $githubUser->avatar,
        ]);
    }
}
