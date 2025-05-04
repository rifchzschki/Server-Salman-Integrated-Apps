<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NewsController extends Controller
{
    public function index()
    {
        return response()->json(News::all(), 200);
    }

    public function show($id)
    {
        $news = News::find($id);
        if (!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }
        return response()->json($news, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|array',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'cover' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'link' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // contoh ambilnya
        // <img src="{{ asset('storage/' . $news->poster) }}" alt="Poster">
        // <img src="{{ asset('storage/' . $news->cover) }}" alt="Cover">

        $posterPath = $request->file('poster')?->store('news_images', 'public');
        $coverPath = $request->file('cover')->store('news_images', 'public');

        $news = News::create([
            'title' => $validatedData['title'],
            'author' => json_encode($validatedData['author']),
            'poster' => $posterPath,
            'cover' => $coverPath,
            'link' => $validatedData['link'],
            'description' => $validatedData['description'] ?? null,
        ]);

        return response()->json(['message' => 'News added successfully', 'news' => $news], 201);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'author' => 'required',
            'link' => 'required|string',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        $validatedData = $request->validate($rules);

        $news = News::findOrFail($id);

        // Parse author from JSON string
        $authors = json_decode($request->input('author'), true);

        $news->title = $request->input('title');
        $news->author = json_encode($authors);
        $news->link = $request->input('link');
        $news->description = $request->input('description');

        // Handle file uploads
        if ($request->hasFile('poster')) {
            $news->poster = $request->file('poster')->store('news_images', 'public');
        }
        if ($request->hasFile('cover')) {
            $news->cover = $request->file('cover')->store('news_images', 'public');
        }

        $news->save();

        return response()->json([
            'message' => 'News updated successfully', 
            'news' => $news
        ]);
    }


    public function destroy($id)
    {
        $news = News::find($id);
        if (!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }

        $news->delete();

        return response()->json(['message' => 'News deleted successfully'], 200);
    }
}
