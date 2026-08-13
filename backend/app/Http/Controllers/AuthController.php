<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\GithubAuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly GithubAuthService $githubAuth,
        private readonly AuthService $auth,
    ) {}


    public function redirectToGithub(): SymfonyRedirectResponse
    {
        return $this->githubAuth->redirect();
    }

    //only place user gets created or authenticated
    public function handleGithubCallbackAndLoginUser(): RedirectResponse
    {
        $frontendUrl = config('app.frontend_url', '/');

        try {
            $githubUser = $this->githubAuth->userFromCallback();

            if (empty($githubUser->email)) {
                return redirect($frontendUrl . '/login?error=email_required');
            }

            $user = $this->githubAuth->resolveUser($githubUser);
            $token = $this->auth->issueApiToken($user);

            return redirect($frontendUrl . '/auth-callback?token=' . $token);
        } catch (Exception $e) {
            return redirect($frontendUrl . '/login?error=auth_failed');
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->auth->revokeCurrentToken($request->user());

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
