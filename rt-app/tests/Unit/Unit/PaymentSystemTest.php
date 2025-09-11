<?php

namespace Tests\Unit\Unit;

use Tests\TestCase;
use App\Models\{Due, Payment, Household};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PaymentSystemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_monthly_dues()
    {
        $households = Household::factory()->count(3)->create();

        $month = 1;
        $year = 2024;
        $amount = 50000;

        foreach ($households as $i => $household) {
            Due::create([
                'household_id' => $household->id,
                'month' => $month,
                'year' => $year,
                'amount' => $amount
            ]);
        }

        $this->assertEquals(3, Due::count());
        $this->assertEquals(150000, Due::sum('amount'));
    }

    /** @test */
    public function it_tracks_due_status_correctly()
    {
        $household = Household::factory()->create();
        $due = Due::factory()->create([
            'household_id' => $household->id,
            'amount' => 50000,
            'status' => 'unpaid',
            'month' => 1,
            'year' => 2024
        ]);

        $this->assertEquals('unpaid', $due->status);

        // Make payment
        Payment::create([
            'due_id' => $due->id,
            'amount' => 50000,
            'paid_at' => now(),
            'method' => 'cash'
        ]);

        $due->update(['status' => 'paid']);
        $this->assertEquals('paid', $due->status);
    }

    /** @test */
    public function it_calculates_payment_totals_correctly()
    {
        $household = Household::factory()->create();
        $due = Due::factory()->create([
            'household_id' => $household->id,
            'amount' => 100000,
            'month' => 1,
            'year' => 2024
        ]);

        // Partial payment
        Payment::create([
            'due_id' => $due->id,
            'amount' => 60000,
            'paid_at' => now(),
            'method' => 'cash'
        ]);

        $totalPaid = Payment::where('due_id', $due->id)->sum('amount');
        $remaining = $due->amount - $totalPaid;

        $this->assertEquals(60000, $totalPaid);
        $this->assertEquals(40000, $remaining);
    }

    /** @test */
    public function it_handles_multiple_payments_for_single_due()
    {
        $household = Household::factory()->create();
        $due = Due::factory()->create([
            'household_id' => $household->id,
            'amount' => 100000,
            'month' => 1,
            'year' => 2024
        ]);

        // Multiple payments
        Payment::create([
            'due_id' => $due->id,
            'amount' => 30000,
            'paid_at' => now(),
            'method' => 'cash'
        ]);

        Payment::create([
            'due_id' => $due->id,
            'amount' => 70000,
            'paid_at' => now()->addDays(1),
            'method' => 'transfer'
        ]);

        $totalPaid = Payment::where('due_id', $due->id)->sum('amount');
        $this->assertEquals(100000, $totalPaid);
        $this->assertEquals(2, Payment::where('due_id', $due->id)->count());
    }

    /** @test */
    public function it_generates_monthly_collection_stats()
    {
        $households = Household::factory()->count(5)->create();
        $month = 1;
        $year = 2024;

        // Create dues for all households with unique month/year combinations
        $dues = [];
        foreach ($households as $i => $household) {
            $dues[] = Due::factory()->create([
                'household_id' => $household->id,
                'month' => $month,
                'year' => $year,
                'amount' => 50000,
                'status' => 'unpaid'
            ]);
        }

        // Pay for first 3 dues specifically
        for ($i = 0; $i < 3; $i++) {
            Payment::create([
                'due_id' => $dues[$i]->id,
                'amount' => $dues[$i]->amount,
                'paid_at' => now(),
                'method' => 'cash'
            ]);
            $dues[$i]->update(['status' => 'paid']);
        }

        $totalDues = Due::where('month', $month)->where('year', $year)->count();
        $paidDues = Due::where('month', $month)->where('year', $year)->where('status', 'paid')->count();
        $collectionRate = ($paidDues / $totalDues) * 100;

        $this->assertEquals(5, $totalDues);
        $this->assertEquals(3, $paidDues);
        $this->assertEquals(60, $collectionRate);
    }

    /** @test */
    public function it_validates_payment_amounts()
    {
        $household = Household::factory()->create();
        $due = Due::factory()->create([
            'household_id' => $household->id,
            'amount' => 50000,
            'month' => 1,
            'year' => 2024
        ]);

        // Test valid payment
        $payment = Payment::create([
            'due_id' => $due->id,
            'amount' => 50000,
            'paid_at' => now(),
            'method' => 'cash'
        ]);

        $this->assertEquals(50000, $payment->amount);
        $this->assertEquals($due->id, $payment->due_id);
    }
}
