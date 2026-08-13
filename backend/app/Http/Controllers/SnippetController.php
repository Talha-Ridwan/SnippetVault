<?php

namespace App\Http\Controllers;

use App\Models\Snippet;
use Illuminate\Http\Request;

class SnippetController extends Controller
{

    public function makeSnippet(Request $request): Snippet
    {
        $validated = $request->validate([
            'title'     => 'required|string',
            'code_body' => 'required|string',
            'language'  => 'required|string',
            'tags'      => 'nullable|array',
        ]);

        return $request->user()->snippets()->create($validated);
    }

    public function getSnippets(Request $request)
    {
        return $request->user()
            ->snippets()
            ->latest()
            ->paginate(15);
    }

    public function deleteSnippet(Request $request, Snippet $snippet)
    {
        $user = $request->user();

        if ($user->id !== $snippet->user_id) {
            abort(403, 'Unauthorized');
        }

        $snippet->delete();

        return response()->json(['message' => 'Snippet deleted successfully']);
    }

    public function updateSnippet(Request $request, Snippet $snippet)
    {
        $user = $request->user();

        if ($user->id !== $snippet->user_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title'     => 'sometimes|required|string',
            'code_body' => 'sometimes|required|string',
            'language'  => 'sometimes|required|string',
            'tags'      => 'nullable|array',
        ]);

        $snippet->update($validated);

        return response()->json([
            'message' => 'Snippet updated successfully', 
            'snippet' => $snippet
        ]);
    }
}