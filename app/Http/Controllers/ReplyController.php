<?php

namespace App\Http\Controllers;
use App\Models\Discussion;
use App\Models\Reply;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($discussionId)
    {
        $discussion = Discussion::find($discussionId);
        if (!$discussion) {
            return response()->json([
                'status' => 404,
                'message' => 'Discussion not found.'
            ], 404);
        }
        $replies = Reply::where('discussion_id', $discussionId)
            ->with('author')
            ->orderByDesc('created_at')
            ->get();

        $formatted = $replies->map(function ($reply) {
            return [
                'id' => $reply->id,
                'content' => $reply->content,
                'author' => trim(($reply->author->first_name ?? '') . ' ' . ($reply->author->last_name ?? '')) ?: 'Unknown',
                'author_id' => $reply->author->id ?? null,
                'created_at' => $reply->created_at
                    ? $reply->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s')
                    : null,
            ];
        });

        return response()->json([
            'status' => 200,
            'message' => 'Replies retrieved successfully.',
            'data' => [
                'discussion' => [
                    'id' => $discussion->id,
                    'title' => $discussion->title,
                    'content' => $discussion->content,
                    'author' => trim(($discussion->user->first_name ?? '') . ' ' . ($discussion->user->last_name ?? '')) ?: 'Unknown',
                    'author_id' => $discussion->user->id ?? null,
                    'created_at' => $discussion->created_at
                        ? $discussion->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s')
                        : null,
                    'updated_at' => $discussion->updated_at
                        ? $discussion->updated_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s')
                        : null,
                ],
                'replies' => $formatted
                ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $discussionId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $reply = Reply::create([
            'discussion_id' => $discussionId,
            'author_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Reply created successfully.',
            'data' => [
                'id' => $reply->id,
                'content' => $reply->content,
                'author_id' => $reply->author_id,
                'created_at' => $reply->created_at
                    ? $reply->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s')
                    : null,
            ]
        ], 201);
    }
}