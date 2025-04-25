<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    //GET
    public function getMonthlyFinance(){
        $now = Carbon::now();
        $currentMonth = $now->format('M');
        $currentYear = $now->format('Y');
        $lastYear = $now->subYear()->format('Y');

        //hapus semua data keuangan dari satu tahun yang lalu biar gak kepenuhan dbnya
        Finance::where('year', $lastYear)->delete();

        //cek apakah bulan dan tahun sekarang datanya ada
        $existing = Finance::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        if (!$existing) { //kalo gak ada buat row baru
            $latest = Finance::latest()->first();
            $monthExpense = $latest ? $latest->monthExpense : 0;
            $monthIncome  = $latest ? $latest->monthIncome : 0;
            $monthCheck   = $latest ? $latest->monthCheck  : 0;

            Finance::create([
                'month' => $currentMonth,
                'year' => $currentYear,
                'monthExpense' => $monthExpense,
                'monthIncome' => $monthIncome,
                'monthCheck' => $monthCheck
            ]);
        }

        $data = Finance::where('month', $currentMonth)->where('year', $currentYear)->first();

        return response()->json([
            'status' => 200,
            'message' => 'Get monthly finance successful',
            'data' => $data
        ], 200);
    }
    //POST

    public function updateMonthlyFinance(Request $request)
    {
        $now = Carbon::now();
        $currentMonth = $now->format('M');
        $currentYear = $now->format('Y');

        $validated = $request->validate([
            'monthExpense' => 'required|integer',
            'monthIncome' => 'required|integer',
            'monthCheck' => 'required|integer',
        ]);

        $finance = Finance::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        if (!$finance) {
            return response()->json(['message' => 'Finance data for this month not found.'], 404);
        }

        $finance->update($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Finance data updated for current month.',
            'data' => $finance
        ]);
    }
}
