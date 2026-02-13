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
            $githubUser = Socialite::driver('github')->stateless()->user();

            $user = User::where('github_id', $githubUser->id)->first();

            if (!$user) {
                $user = User::where('email', $githubUser->email)->first();

                if ($user) {
                    $user->update([
                        'github_id' => $githubUser->id,
                        'avatar' => $githubUser->avatar
                    ]);
                } else {

                    $user = User::create([
                        'name' => $githubUser->name ?? $githubUser->nickname,
                        'email' => $githubUser->email,
                        'github_id' => $githubUser->id,
                        'avatar' => $githubUser->avatar,
                    ]);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User logged in successfully',
                'user' => $user,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication failed',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
