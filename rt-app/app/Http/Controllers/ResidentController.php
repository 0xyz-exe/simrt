<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Models\Household;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    public function index()
    {
        $residents = Resident::with('household')->latest()->get();
        return view('residents.index', compact('residents'));
    }

    public function create()
    {
        $households = Household::all();
        return view('residents.create', compact('households'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|unique:residents',
            'nama' => 'required|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'household_id' => 'required|exists:households,id',
            'is_head' => 'boolean'
        ]);

        $validated['is_head'] = $request->has('is_head');

        // If this resident is head, unset previous head
        if ($validated['is_head']) {
            Resident::where('household_id', $validated['household_id'])
                ->update(['is_head' => false]);

            // Update household kepala_keluarga_id will be handled after creation
        }

        $resident = Resident::create($validated);

        // Update household kepala_keluarga_id if this is head
        if ($validated['is_head']) {
            $resident->household()->update(['kepala_keluarga_id' => $resident->id]);
        }

        return redirect()->route('residents.index')
            ->with('success', 'Data warga berhasil ditambahkan.');
    }

    public function show(Resident $resident)
    {
        $resident->load('household');
        return view('residents.show', compact('resident'));
    }

    public function edit(Resident $resident)
    {
        $households = Household::all();
        return view('residents.edit', compact('resident', 'households'));
    }

    public function update(Request $request, Resident $resident)
    {
        $validated = $request->validate([
            'nik' => 'required|string|unique:residents,nik,' . $resident->id,
            'nama' => 'required|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'household_id' => 'required|exists:households,id',
            'is_head' => 'boolean'
        ]);

        $validated['is_head'] = $request->has('is_head');

        // If this resident is becoming head, unset previous head
        if ($validated['is_head'] && !$resident->is_head) {
            Resident::where('household_id', $validated['household_id'])
                ->where('id', '!=', $resident->id)
                ->update(['is_head' => false]);
        }

        $resident->update($validated);

        // Update household kepala_keluarga_id
        if ($validated['is_head']) {
            $resident->household()->update(['kepala_keluarga_id' => $resident->id]);
        } elseif ($resident->household->kepala_keluarga_id === $resident->id) {
            $resident->household()->update(['kepala_keluarga_id' => null]);
        }

        return redirect()->route('residents.show', $resident)
            ->with('success', 'Data warga berhasil diperbarui.');
    }

    public function destroy(Resident $resident)
    {
        // If this resident is head, unset household kepala_keluarga_id
        if ($resident->household->kepala_keluarga_id === $resident->id) {
            $resident->household()->update(['kepala_keluarga_id' => null]);
        }

        $resident->delete();
        return redirect()->route('residents.index')
            ->with('success', 'Data warga berhasil dihapus.');
    }
}
