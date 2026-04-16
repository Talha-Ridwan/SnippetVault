<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Socialite\Contracts\User as SocialiteUser;
class AuthController extends Controller
{
    //api/auth/github/redirect
    public function redirectToGithub(Request $request)
    {
        $key = 'login_attempts:' . $request->ip();
        $currentAttempts = Redis::incr($key);

        if ($currentAttempts === 1) {
            Redis::expire($key, 60);
        }

        if ($currentAttempts > 5) {
            return response()->json([
                'message' => 'Too many login attempts. Chill out.'
            ], 429);
        }

        return Socialite::driver('github')->stateless()->redirect();
    }

    //api/auth/github/callback
    public function handleGithubCallbackAndLoginUser()
    {
        try {
            $githubUser = Socialite::driver('github')->stateless()->user();
            $user = $this->resolveGithubUser($githubUser);
            return $this->loginUser($user);

        } catch (Exception $e) {
            return redirect(config('app.frontend_url'). '/login?error=auth_failed');
        }
    }
    protected function resolveGithubUser(SocialiteUser $githubUser): User
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
                'avatar' => $githubUser->avatar
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

    protected function loginUser(User $user)
    {
        $token = $user->createToken('auth_token')->plainTextToken;
        $frontendUrl = config('app.frontend_url', '/');
        return redirect($frontendUrl .'/auth-callback?token='. $token);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request){
        return response()->json($request->user());
    }
}
