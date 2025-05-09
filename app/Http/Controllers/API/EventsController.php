<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
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
            'link' => 'nullable|url',
            'cover_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Convert 'is_online' to boolean (true or false)
        $data['is_online'] = filter_var($data['is_online'], FILTER_VALIDATE_BOOLEAN);

        // Handle upload cover image
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('events/covers', 'public');
            $data['cover_image'] = asset('storage/' . $coverPath);
        }

        // Handle upload poster
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('events/posters', 'public');
            $data['poster'] = asset('storage/' . $posterPath);
        }

        $event = Event::create($data);

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
            'link' => 'nullable|url',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Handle upload cover image
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('events/covers', 'public');
            $data['cover_image'] = asset('storage/' . $coverPath);
        }

        // Handle upload poster
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('events/posters', 'public');
            $data['poster'] = asset('storage/' . $posterPath);
        }

        $event->update($data);

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
