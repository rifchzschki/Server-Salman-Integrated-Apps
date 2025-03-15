<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visitor;

class VisitorController extends Controller
{
    // get
    // return list of date, month(string), year, amount (jumlah pengunjung di hari itu)
    public function getMonthlyVisitors(Request $request)
    {
        $month = $request->query('month');
        $year = $request->query('year');

        $visitors = Visitor::whereYear('date', $year)
            ->whereMonth('date', date('m', strtotime($month)))
            ->get()
            ->map(function ($visitor) {
                return [
                    'date' => date('d', strtotime($visitor->date)),
                    'month' => date('F', strtotime($visitor->date)),
                    'year' => date('Y', strtotime($visitor->date)),
                    'amount' => $visitor->amount
                ];
            });

        return response()->json($visitors, 200);
    }

    // post
    public function addVisitors(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|integer|min:0'
        ]);

        $visitor = Visitor::updateOrCreate(
            ['date' => $validated['date']],
            ['amount' => $validated['amount']]
        );

        return response()->json([
            'date' => date('d', strtotime($visitor->date)),
            'month' => date('F', strtotime($visitor->date)),
            'year' => date('Y', strtotime($visitor->date)),
            'amount' => $visitor->amount
        ], 201);
    }

    // patch
    public function updateVisitors(Request $request)
    {
        $request->validate([
            'date' => 'required|integer|min:1|max:31',
            'month' => 'required|string',
            'year' => 'required|integer',
            'amount' => 'required|integer|min:0'
        ]);

        $formattedDate = sprintf('%04d-%02d-%02d', $request->year, date('m', strtotime($request->month)), $request->date);

        $visitor = Visitor::where('date', $formattedDate)->first();
        if (!$visitor) {return response()->json(['message' => 'Visitor record not found'], 404);}
        $visitor->update(['amount' => $request->amount]);

        return response()->json([
            'date' => date('d', strtotime($visitor->date)),
            'month' => date('F', strtotime($visitor->date)),
            'year' => date('Y', strtotime($visitor->date)),
            'amount' => $visitor->amount
        ], 200);
    }
}