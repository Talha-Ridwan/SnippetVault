<?php

namespace App\Http\Controllers;

use App\Models\Snippet;
use App\Services\SnippetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SnippetController extends Controller
{
    public function __construct(
        private readonly SnippetService $snippetService,
    ) {}

    public function makeSnippet(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'     => 'required|string',
            'code_body' => 'required|string',
            'language'  => 'required|string',
            'tags'      => 'nullable|array',
        ]);

        $snippet = $this->snippetService->createForUser($request->user(), $validated);

        return response()->json($snippet, 201);
    }

    public function getSnippets(Request $request): JsonResponse
    {
        return response()->json(
            $this->snippetService->paginateForUser($request->user())
        );
    }

    public function deleteSnippet(Request $request, Snippet $snippet): JsonResponse
    {
        $this->snippetService->deleteForUser($request->user(), $snippet);

        return response()->json(['message' => 'Snippet deleted successfully']);
    }

    public function updateSnippet(Request $request, Snippet $snippet): JsonResponse
    {
        $validated = $request->validate([
            'title'     => 'sometimes|required|string',
            'code_body' => 'sometimes|required|string',
            'language'  => 'sometimes|required|string',
            'tags'      => 'nullable|array',
        ]);

        $snippet = $this->snippetService->updateForUser($request->user(), $snippet, $validated);

        return response()->json([
            'message' => 'Snippet updated successfully',
            'snippet' => $snippet,
        ]);
    }
    public function getSnippetByTag(Request $request): JsonResponse
    {   
        $user = $request->user();

        if($user === null) {
            return response()->json(['message' => 'Unauthorized'], 401);
        };

        $validated = $request->validate([
            'tags' => 'required|array',
        ]);

        return response()->json(
            $this->snippetService->getSnippetByTag($user, $validated['tags'])
        );
    }
}
