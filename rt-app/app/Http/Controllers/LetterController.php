<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Letter;
use App\Models\Resident;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\PdfService;
use App\Notifications\LetterStatusNotification;

class LetterController extends Controller
{
    public function index(Request $request)
    {
        // Admin melihat semua pengajuan surat
        Gate::authorize('admin');

        $query = Letter::with('resident.household')
            ->orderBy('created_at', 'desc');

        // Filter by status if provided
        if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $letters = $query->paginate(10);

        return view('letters.index', compact('letters'));
    }

    public function create()
    {
        // Warga mengajukan surat baru
        Gate::authorize('resident');

        $resident = Auth::user()->resident;
        if (!$resident) {
            return redirect()->back()->with('error', 'Anda harus terdaftar sebagai penduduk untuk mengajukan surat.');
        }

        return view('letters.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('resident');

        $request->validate([
            'type' => 'required|in:domisili,pengantar,keterangan',
            'purpose' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500'
        ]);

        $resident = Auth::user()->resident;
        if (!$resident) {
            return redirect()->back()->with('error', 'Anda harus terdaftar sebagai penduduk untuk mengajukan surat.');
        }

        Letter::create([
            'resident_id' => $resident->id,
            'type' => $request->type,
            'purpose' => $request->purpose,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        return redirect()->route('letters.mine')->with('success', 'Pengajuan surat berhasil disubmit. Menunggu persetujuan admin.');
    }

    public function mine()
    {
        // Warga melihat surat mereka sendiri
        Gate::authorize('resident');

        $resident = Auth::user()->resident;
        if (!$resident) {
            return redirect()->back()->with('error', 'Anda harus terdaftar sebagai penduduk.');
        }

        $letters = Letter::where('resident_id', $resident->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('letters.mine', compact('letters'));
    }

    public function approve(Letter $letter, PdfService $pdfService)
    {
        Gate::authorize('admin');

        $letter->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        // Generate PDF automatically when approved
        try {
            $pdfService->generateLetterPdf($letter);
        } catch (\Exception $e) {
            // Log error but don't fail the approval
            logger()->error('Failed to generate PDF for letter ' . $letter->id . ': ' . $e->getMessage());
        }

        // Send notification to user
        if ($letter->resident->user) {
            $letter->resident->user->notify(new LetterStatusNotification($letter, 'approved'));
        }

        return redirect()->back()->with('success', 'Surat telah disetujui, PDF telah digenerate, dan notifikasi telah dikirim.');
    }

    public function reject(Letter $letter)
    {
        Gate::authorize('admin');

        $letter->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        // Send notification to user
        if ($letter->resident->user) {
            $letter->resident->user->notify(new LetterStatusNotification($letter, 'rejected'));
        }

        return redirect()->back()->with('success', 'Surat telah ditolak dan notifikasi telah dikirim.');
    }

    public function show(Letter $letter)
    {
        // Admin dapat melihat semua surat, resident hanya milik mereka
        if (Gate::allows('admin') ||
            (Gate::allows('resident') && $letter->resident_id === Auth::user()->resident?->id)) {

            return view('letters.show', compact('letter'));
        }

        abort(403);
    }

    public function downloadPdf(Letter $letter, PdfService $pdfService)
    {
        // Check authorization
        if (Gate::allows('admin') ||
            (Gate::allows('resident') && $letter->resident_id === Auth::user()->resident?->id)) {

            // Only allow download for approved letters
            if ($letter->status !== 'approved') {
                return redirect()->back()->with('error', 'PDF hanya tersedia untuk surat yang telah disetujui.');
            }

            return $pdfService->downloadPdf($letter);
        }

        abort(403);
    }
}
