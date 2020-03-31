<?php

namespace Plmrlnsnts\Commentator\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Plmrlnsnts\Commentator\Comment;
use Plmrlnsnts\Commentator\Facades\Commentator;

class CommentsController
{
    /**
     * Get all comments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Commentator::getCommentable(request('commentableKey'))
            ->comments()
            ->with('author')
            ->withCount('replies')
            ->paginate(request('perPage'));
    }

    /**
     * Store a new comment.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attributes = $this->validateRequest($request);

        $commentable = Commentator::getCommentable(request('commentableKey'));

        return $commentable->addComment($attributes);
    }

    /**
     * Update a comment.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Plmrlnsnts\Commentator\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        Gate::authorize('update', $comment);

        $attributes = $this->validateRequest($request);

        return tap($comment)->update($attributes);
    }

    /**
     * Delete a comment.
     *
     * @param \Plmrlnsnts\Commentator\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return response()->noContent();
    }

    /**
     * Validate the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'body' => ['required', 'string'],
            'media' => ['nullable', 'url'],
        ]);
    }
}