<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToGithub()
    {
        return Socialite::driver('github')
            ->stateless()
            ->redirect();
    }

    public function handleGithubCallback()
    {
        try {
            // 1. Get the raw user from GitHub
            $githubUser = Socialite::driver('github')->stateless()->user();
            
            // 2. Resolve the user in your database (Find or Create)
            $user = $this->resolveGithubUser($githubUser);
            
            // 3. Log the user in and return the API response
            return $this->loginUser($user);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication failed',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Resolves the user from the database based on GitHub data.
     *
     * @param \Laravel\Socialite\Contracts\User $githubUser
     * @return \App\Models\User
     */
    protected function resolveGithubUser($githubUser)
    {
        // Check by GitHub ID
        $user = User::where('github_id', $githubUser->id)->first();

        if ($user) {
            return $user;
        }

        // Check by Email (Account Linking)
        $user = User::where('email', $githubUser->email)->first();

        if ($user) {
            $user->update([
                'github_id' => $githubUser->id,
                'avatar' => $githubUser->avatar
            ]);
            
            return $user;
        }

        // Create New User
        return User::create([
            'name' => $githubUser->name ?? $githubUser->nickname,
            'email' => $githubUser->email,
            'github_id' => $githubUser->id,
            'avatar' => $githubUser->avatar,
        ]);
    }

    /**
     * Handles the token creation and JSON response formatting.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loginUser(User $user)
    {
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}