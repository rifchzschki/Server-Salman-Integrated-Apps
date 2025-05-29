<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\Reply;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $discussions = Discussion::with('user')->orderByDesc('updated_at')->get();
        $formatted = $discussions->map(function ($discussion) {
            return [
                'id' => $discussion->id,
                'title' => $discussion->title,
                'content' => $discussion->content,
                'author' => trim(($discussion->user->first_name ?? '') . ' ' . ($discussion->user->last_name ?? '')) ?: 'Unknown',
                'author_id' => $discussion->user->id ?? null,
                'likes' => $discussion->likes,
                'created_at' => $discussion->created_at
                    ? $discussion->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s')
                    : null,
                'updated_at' => $discussion->updated_at
                    ? $discussion->updated_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s')
                    : null,
            ];
        });
        return response()->json([
            'status' => 200,
            'message' => 'Discussions retrieved successfully.',
            'data' => $formatted
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $discussion = Discussion::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Discussion created successfully.',
            'data' => $discussion->load('user')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Discussion $discussion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discussion $discussion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Discussion $discussion)
    {
        if ($request->user()->id !== $discussion->user_id && $request->user()->role !== 'admin') {
            return response()->json(['status' => 403, 'message' => 'Unauthorized'], 403);
        }
    
        $request->validate(['content' => 'required|string']);
    
        $discussion->update(['content' => $request->content]);
    
        return response()->json([
            'status' => 200,
            'message' => 'Discussion updated successfully.',
            'data' => $discussion
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discussion $discussion, Request $request)
    {
        if ($request->user()->id !== $discussion->user_id && $request->user()->role !== 'admin') {
            return response()->json(['status' => 403, 'message' => 'Unauthorized'], 403);
        }
    
        $discussion->delete();
    
        return response()->json([
            'status' => 200,
            'message' => 'Discussion deleted successfully.'
        ]);
    }
}
