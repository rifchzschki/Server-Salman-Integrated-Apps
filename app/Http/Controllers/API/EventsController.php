<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\Event;
use Cloudinary\Api\Upload\UploadApi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all();
        return response()->json($events);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'organizer' => 'nullable|string|max:255',
            'is_online' => 'required|string',
            'link' => 'nullable|string',
            'cover_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Convert 'is_online' to boolean (true or false)
        $data['is_online'] = filter_var($data['is_online'], FILTER_VALIDATE_BOOLEAN);

        $uploadApi = new UploadApi();
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');

            try {
                // getRealPath() memberikan path sementara file yang diupload
                $uploadResult = $uploadApi->upload($file->getRealPath(), [
                    'public_id' => 'events/' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time(), // Contoh public_id unik
                    'overwrite' => true, // Timpa jika public_id sudah ada
                    // 'use_filename' => true, // Jika ingin menggunakan nama file asli sebagai dasar public_id (perhatikan potensi konflik nama)
                    // 'folder' => 'my_folder_in_cloudinary' // Jika ingin menyimpan dalam folder tertentu
                ]);

                // 'secure_url' adalah URL HTTPS, 'url' adalah HTTP
                $coverUrl = $uploadResult['secure_url'];
                $coverPublicId = $uploadResult['public_id'];


                Log::info("Event Controller: " ,[$coverUrl]);
                Log::info("Event Controller: " ,[$coverPublicId]);
            }catch (Exception $e) {
                return response()->json(['message' => 'Gagal mengupload cover: '. $e->getMessage()], 500);

            }
        }

        if ($request->hasFile('poster')) {
            $file = $request->file('poster');

            try {
                // getRealPath() memberikan path sementara file yang diupload
                $uploadResult = $uploadApi->upload($file->getRealPath(), [
                    'public_id' => 'events/' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time(), // Contoh public_id unik
                    'overwrite' => true, // Timpa jika public_id sudah ada
                    // 'use_filename' => true, // Jika ingin menggunakan nama file asli sebagai dasar public_id (perhatikan potensi konflik nama)
                    // 'folder' => 'my_folder_in_cloudinary' // Jika ingin menyimpan dalam folder tertentu
                ]);

                // 'secure_url' adalah URL HTTPS, 'url' adalah HTTP
                $posterUrl = $uploadResult['secure_url'];
                $posterPublicId = $uploadResult['public_id'];


                Log::info("Event Controller: " ,[$posterUrl]);
                Log::info("Event Controller: " ,[$posterPublicId]);
            }catch (Exception $e) {
                return response()->json(['message' => 'Gagal mengupload poster: '. $e->getMessage()], 500);

            }
        }
        $event = Event::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'location' => $data['location'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'organizer' => $data['organizer'],
            'is_online' => $data['is_online'],
            'link' => $data['link'],
            'cover_image' => $coverUrl,
            'poster' => $posterUrl,
            'cover_image_public_id' => $coverPublicId,
            'poster_public_id' => $posterPublicId,
        ]);

        Log::info("Event created: ", [$event]);

        return response()->json([
            'message' => 'Event created successfully!',
            'data' => $event
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after_or_equal:start_time',
            'organizer' => 'nullable|string|max:255',
            'is_online' => 'sometimes|required|boolean',
            'link' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $event->update(collect($data)->except(['cover_image', 'poster'])->toArray());

        if ($request->hasFile('cover_image')) {
            $event->uploadCoverImage($request->file('cover_image'));
        }

        if ($request->hasFile('poster')) {
            $event->uploadPoster($request->file('poster'));
        }

        return response()->json([
            'message' => 'Event updated successfully!',
            'data' => $event
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully!']);
    }
}
