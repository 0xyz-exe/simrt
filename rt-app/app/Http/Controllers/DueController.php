<?php

namespace App\Http\Controllers;

use App\Models\Due;
use App\Models\Household;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DueController extends Controller
{
    public function index(Request $request)
    {
        // Validate and sanitize input parameters
        $validated = $request->validate([
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|between:2020,2030',
            'status' => 'nullable|in:paid,unpaid'
        ]);

        $query = Due::with(['household.kepalaKeluarga', 'payments']);

        // Use validated input only
        if (!empty($validated['month'])) {
            $query->where('month', $validated['month']);
        }
        if (!empty($validated['year'])) {
            $query->where('year', $validated['year']);
        }
        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        // Default to current month/year if no filter
        if (empty($validated['month']) && empty($validated['year'])) {
            $query->where('month', date('n'))->where('year', date('Y'));
        }

        $dues = $query->orderBy('household_id')->get();

        // Stats for current filter
        $totalDues = $dues->count();
        $paidDues = $dues->where('status', 'paid')->count();
        $totalAmount = $dues->sum('amount');
        $paidAmount = $dues->where('status', 'paid')->sum('amount');

        return view('dues.index', compact('dues', 'totalDues', 'paidDues', 'totalAmount', 'paidAmount'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
            'amount' => 'required|integer|min:0'
        ]);

        $households = Household::all();
        $created = 0;
        $skipped = 0;

        foreach ($households as $household) {
            $existing = Due::where([
                'household_id' => $household->id,
                'month' => $validated['month'],
                'year' => $validated['year']
            ])->exists();

            if (!$existing) {
                Due::create([
                    'household_id' => $household->id,
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                    'amount' => $validated['amount'],
                    'status' => 'unpaid'
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        return back()->with('success', "Iuran berhasil digenerate. $created iuran baru dibuat, $skipped sudah ada.");
    }

    public function mine()
    {
        // For residents to view their household's dues
        $user = Auth::user();
        $resident = \App\Models\Resident::where('email', $user->email)->first();

        if (!$resident) {
            return view('dues.mine')->with('error', 'Data warga tidak ditemukan.');
        }

        $dues = Due::where('household_id', $resident->household_id)
            ->with('payments')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('dues.mine', compact('dues', 'resident'));
    }
}
