<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HouseholdController extends Controller
{
    public function index()
    {
        $households = Household::with(['residents', 'kepalaKeluarga'])->latest()->get();
        return view('households.index', compact('households'));
    }

    public function create()
    {
        return view('households.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_kk' => 'required|string|unique:households',
            'alamat' => 'required|string',
            'rt' => 'required|string|max:4',
            'rw' => 'required|string|max:4',
        ]);

        $household = Household::create($validated);

        return redirect()->route('households.show', $household)
            ->with('success', 'Data keluarga berhasil ditambahkan.');
    }

    public function show(Household $household)
    {
        $household->load(['residents', 'kepalaKeluarga', 'dues.payments']);
        return view('households.show', compact('household'));
    }

    public function edit(Household $household)
    {
        return view('households.edit', compact('household'));
    }

    public function update(Request $request, Household $household)
    {
        $validated = $request->validate([
            'nomor_kk' => 'required|string|unique:households,nomor_kk,' . $household->id,
            'alamat' => 'required|string',
            'rt' => 'required|string|max:4',
            'rw' => 'required|string|max:4',
        ]);

        $household->update($validated);

        return redirect()->route('households.show', $household)
            ->with('success', 'Data keluarga berhasil diperbarui.');
    }

    public function destroy(Household $household)
    {
        $household->delete();
        return redirect()->route('households.index')
            ->with('success', 'Data keluarga berhasil dihapus.');
    }

    public function mine()
    {
        // For residents to view their own household
        $user = Auth::user();
        $resident = Resident::where('email', $user->email)->first();

        if (!$resident) {
            return view('households.mine')->with('error', 'Data warga tidak ditemukan.');
        }

        $household = $resident->household;
        $household->load(['residents', 'kepalaKeluarga', 'dues.payments']);

        return view('households.mine', compact('household'));
    }
}
