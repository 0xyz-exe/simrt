<?php

namespace App\Http\Controllers;

use App\Models\Due;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'due_id' => 'required|exists:dues,id',
            'amount' => 'required|integer|min:1',
            'method' => 'required|in:cash,transfer,qris',
            'paid_at' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $due = Due::findOrFail($validated['due_id']);

        // Create payment record
        Payment::create([
            'due_id' => $due->id,
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'paid_at' => $validated['paid_at'],
            'notes' => $validated['notes']
        ]);

        // Update due status to paid
        $due->update(['status' => 'paid']);

        return back()->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function destroy(Payment $payment)
    {
        $due = $payment->due;

        // Delete payment
        $payment->delete();

        // Check if there are other payments for this due
        if ($due->payments()->count() == 0) {
            $due->update(['status' => 'unpaid']);
        }

        return back()->with('success', 'Pembayaran berhasil dihapus.');
    }
}
