<?php

namespace App\Services;

use App\Models\Snippet;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SnippetService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createForUser(User $user, array $attributes): Snippet
    {
        return $user->snippets()->create($attributes);
    }

    /**
     * @return LengthAwarePaginator<int, Snippet>
     */
    public function paginateForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $user->snippets()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws AuthorizationException
     */
    public function updateForUser(User $user, Snippet $snippet, array $attributes): Snippet
    {
        $this->assertOwnedBy($user, $snippet);

        $snippet->update($attributes);

        return $snippet;
    }

    /**
     * @throws AuthorizationException
     */
    public function deleteForUser(User $user, Snippet $snippet): void
    {
        $this->assertOwnedBy($user, $snippet);

        $snippet->delete();
    }

    /**
     * @throws AuthorizationException
     */
    private function assertOwnedBy(User $user, Snippet $snippet): void
    {
        if ($user->id !== $snippet->user_id) {
            throw new AuthorizationException('Unauthorized');
        }
    }

    private function getSnippetByTag(User $user, array $tags): LengthAwarePaginator
    {
        return $user->snippets()
        ->whereHas('tags', function ($query) use ($tags) {
            $query->whereIn('name', $tags);
        })
        ->latest()
        ->paginate(15);
    }
}
