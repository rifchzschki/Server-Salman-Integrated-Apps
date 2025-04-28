<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class QuotesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Quote::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $quote = Quote::create([
            'content' => $request->content,
        ]);

        return response()->json($quote, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $quote = Quote::findOrFail($id);
        return response()->json($quote);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $quote = Quote::findOrFail($id);
        $quote->update([
            'content' => $request->content,
        ]);

        return response()->json($quote);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $quote = Quote::findOrFail($id);
        $quote->delete();

        return response()->json(['message' => 'Quote deleted successfully']);
    }
}
