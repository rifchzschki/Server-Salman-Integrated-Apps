<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Cloudinary\Api\Upload\UploadApi;
use Exception;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::all();
        return response()->json($news, 200);
    }

    public function show($news_id)
    {
        $news = News::find($news_id);
        if (!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }
        return response()->json($news, 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'author' => 'required|array',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'cover' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'link' => 'required|string',
            'description' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        $uploadApi = new UploadApi();

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');

            try {
                // getRealPath() memberikan path sementara file yang diupload
                $uploadResult = $uploadApi->upload($file->getRealPath(), [
                    'public_id' => 'news/' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time(), // Contoh public_id unik
                    'overwrite' => true, // Timpa jika public_id sudah ada
                    // 'use_filename' => true, // Jika ingin menggunakan nama file asli sebagai dasar public_id (perhatikan potensi konflik nama)
                    // 'folder' => 'my_folder_in_cloudinary' // Jika ingin menyimpan dalam folder tertentu
                ]);

                // 'secure_url' adalah URL HTTPS, 'url' adalah HTTP
                $coverUrl = $uploadResult['secure_url'];
                $coverPublicId = $uploadResult['public_id'];


                Log::info("News Controller: " ,[$coverUrl]);
                Log::info("News Controller: " ,[$coverPublicId]);
            }catch (Exception $e) {
                return response()->json(['message' => 'Gagal mengupload cover: '. $e->getMessage()], 500);

            }
        }
        if ($request->hasFile('poster')) {
            $file = $request->file('poster');

            try {
                // getRealPath() memberikan path sementara file yang diupload
                $uploadResult = $uploadApi->upload($file->getRealPath(), [
                    'public_id' => 'news/' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time(), // Contoh public_id unik
                    'overwrite' => true, // Timpa jika public_id sudah ada
                    // 'use_filename' => true, // Jika ingin menggunakan nama file asli sebagai dasar public_id (perhatikan potensi konflik nama)
                    // 'folder' => 'my_folder_in_cloudinary' // Jika ingin menyimpan dalam folder tertentu
                ]);

                // 'secure_url' adalah URL HTTPS, 'url' adalah HTTP
                $posterUrl = $uploadResult['secure_url'];
                $posterPublicId = $uploadResult['public_id'];


                Log::info("News Controller: " ,[$posterUrl]);
                Log::info("News Controller: " ,[$posterPublicId]);
            }catch (Exception $e) {
                return response()->json(['message' => 'Gagal mengupload poster: '. $e->getMessage()], 500);

            }
        }

        $news = News::create([
            'title' => $validatedData['title'],
            'author' => $validatedData['author'],
            'poster' => $posterUrl,
            'cover' => $coverUrl,
            'link' => $validatedData['link'],
            'description' => $validatedData['description'] ?? null,
            'poster_public_id' => $posterPublicId,
            'cover_public_id' => $coverPublicId,
        ]);

        $news->makeHidden(['poster_public_id', 'cover_public_id']);

        return response()->json(['message' => 'News added successfully', 'news' => $news], 201);
        // return response()->json(['message' => 'News added successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);
        Log::info("update news: ", [$request->all()]);
        Log::info("news: ", [$news]);


        $rules = [
            'title' => 'nullable|string|max:255',
            'author' => 'nullable|array',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'link' => 'nullable|string',
            'description' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        Log::info("news(before upload): ", [$news]);

        $uploadApi = new UploadApi();

        if ($request->hasFile('cover')) {
            // Hapus file lama
            try{
                if ($news->cover_public_id) {
                    $uploadApi->destroy($news->cover_public_id);
                }

                $file = $request->file('cover');
                $uploadResult = $uploadApi->upload($file->getRealPath(), [
                    'public_id' => 'news/' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time(),
                    'overwrite' => true,
                ]);

                $news->cover = $uploadResult['secure_url'];
                $news->cover_public_id = $uploadResult['public_id'];
            }catch (Exception $e) {
                Log::error("Change cover: ", [$e]);
                return response()->json(['message' => 'Gagal mengupload cover: '. $e->getMessage()], 500);

            }
        }

        if ($request->hasFile('poster')) {
            try{
                if ($news->poster_public_id) {
                    $uploadApi->destroy($news->poster_public_id);
                }

                $file = $request->file('poster');
                $uploadResult = $uploadApi->upload($file->getRealPath(), [
                    'public_id' => 'news/' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time(),
                    'overwrite' => true,
                ]);

                $news->poster = $uploadResult['secure_url'];
                $news->poster_public_id = $uploadResult['public_id'];
            }catch (Exception $e) {
                Log::error("Change poster: ", [$e]);
                return response()->json(['message' => 'Gagal mengupload poster: '. $e->getMessage()], 500);

            }
        }

        Log::info("news(after upload): ", [$news]);
        $news->title = $validatedData['title'];
        $news->author = $validatedData['author'];
        $news->link = $validatedData['link'];
        $news->description = $validatedData['description'] ?? null;

        $news->save();

        Log::info("news(after save): ", [$news]);

        $news->makeHidden(['poster_public_id', 'cover_public_id']);

        return response()->json(['message' => 'News updated successfully', 'news' => $news]);
    }

    public function destroy($news_id)
    {
        $news = News::findOrFail($news_id);
        $uploadApi = new UploadApi();
        $uploadApi->destroy($news->cover_public_id);
        $uploadApi->destroy($news->poster_public_id);
        $news->delete();

        return response()->json([
            'message' => 'News deleted successfully'
        ], 200);
    }
}
