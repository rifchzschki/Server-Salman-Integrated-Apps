<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 200,
            'message' => 'Discussions retrieved successfully.',
            'data' => Discussion::with('user')->orderByDesc('created_at')->get()
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
        $request->validate(['content' => 'required|string']);

        $discussion = Discussion::create([
            'user_id' => $request->user()->id,
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
    public function destroy(Discussion $discussion)
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
