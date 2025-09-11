<?php

namespace Tests\Feature\Feature;

use Tests\TestCase;
use App\Models\{User, Household, Due, Payment};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PaymentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $household;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'admin'
        ]);

        $this->household = Household::factory()->create();
    }

    /** @test */
    public function admin_can_view_dues_dashboard()
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/dues');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dues.index');
        $response->assertSee('Manajemen Iuran');
    }

    /** @test */
    public function admin_can_create_monthly_dues_for_all_households()
    {
        $this->actingAs($this->admin);

        $households = Household::factory()->count(5)->create();

        $dueData = [
            'month' => now()->month,
            'year' => now()->year,
            'amount' => 50000,
            'description' => 'Iuran bulanan test'
        ];

        $response = $this->post('/admin/dues/generate', $dueData);

        $response->assertRedirect('/admin/dues');
        $this->assertEquals(6, Due::count()); // 5 new + 1 from setup
    }

    /** @test */
    public function admin_can_record_payment()
    {
        $this->actingAs($this->admin);

        $due = Due::factory()->create([
            'household_id' => $this->household->id,
            'amount' => 50000,
            'status' => 'unpaid'
        ]);

        $paymentData = [
            'amount' => 50000,
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'notes' => 'Pembayaran tunai'
        ];

        $response = $this->post("/admin/dues/{$due->id}/payment", $paymentData);

        $response->assertRedirect("/admin/dues/{$due->id}");

        $due->refresh();
        $this->assertEquals('paid', $due->status);
        $this->assertEquals(1, Payment::count());
    }

    /** @test */
    public function admin_can_record_partial_payment()
    {
        $this->actingAs($this->admin);

        $due = Due::factory()->create([
            'household_id' => $this->household->id,
            'amount' => 100000,
            'status' => 'unpaid'
        ]);

        // First partial payment
        $paymentData1 = [
            'amount' => 60000,
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash'
        ];

        $response = $this->post("/admin/dues/{$due->id}/payment", $paymentData1);
        $response->assertRedirect("/admin/dues/{$due->id}");

        $due->refresh();
        $this->assertEquals('partial', $due->status);

        // Second payment to complete
        $paymentData2 = [
            'amount' => 40000,
            'payment_date' => now()->addDays(1)->format('Y-m-d'),
            'payment_method' => 'transfer'
        ];

        $response = $this->post("/admin/dues/{$due->id}/payment", $paymentData2);

        $due->refresh();
        $this->assertEquals('paid', $due->status);
        $this->assertEquals(2, Payment::where('due_id', $due->id)->count());
    }

    /** @test */
    public function admin_can_view_payment_history()
    {
        $this->actingAs($this->admin);

        $due = Due::factory()->create([
            'household_id' => $this->household->id
        ]);

        $payment = Payment::factory()->create([
            'due_id' => $due->id,
            'amount' => 50000,
            'payment_method' => 'cash'
        ]);

        $response = $this->get("/admin/dues/{$due->id}");

        $response->assertStatus(200);
        $response->assertSee('50000');
        $response->assertSee('cash');
    }

    /** @test */
    public function admin_can_filter_dues_by_status()
    {
        $this->actingAs($this->admin);

        Due::factory()->create(['status' => 'paid', 'household_id' => $this->household->id]);
        Due::factory()->create(['status' => 'unpaid', 'household_id' => $this->household->id]);
        Due::factory()->create(['status' => 'partial', 'household_id' => $this->household->id]);

        $response = $this->get('/admin/dues?status=unpaid');

        $response->assertStatus(200);
        $response->assertSee('unpaid');
    }

    /** @test */
    public function admin_can_view_monthly_collection_report()
    {
        $this->actingAs($this->admin);

        $households = Household::factory()->count(3)->create();
        $month = now()->month;
        $year = now()->year;

        // Create dues for current month
        foreach ($households as $household) {
            $due = Due::factory()->create([
                'household_id' => $household->id,
                'month' => $month,
                'year' => $year,
                'amount' => 50000
            ]);

            // Pay for first 2 households
            if ($household->id <= 2) {
                Payment::factory()->create(['due_id' => $due->id, 'amount' => 50000]);
                $due->update(['status' => 'paid']);
            }
        }

        $response = $this->get("/admin/dues/report?month={$month}&year={$year}");

        $response->assertStatus(200);
        $response->assertSee('Laporan Iuran');
    }

    /** @test */
    public function admin_can_export_payment_data()
    {
        $this->actingAs($this->admin);

        $due = Due::factory()->create(['household_id' => $this->household->id]);
        Payment::factory()->create(['due_id' => $due->id]);

        $response = $this->get('/admin/payments/export');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    /** @test */
    public function payment_amount_validation_works()
    {
        $this->actingAs($this->admin);

        $due = Due::factory()->create([
            'household_id' => $this->household->id,
            'amount' => 50000
        ]);

        // Try to pay more than due amount
        $paymentData = [
            'amount' => 60000,
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash'
        ];

        $response = $this->post("/admin/dues/{$due->id}/payment", $paymentData);

        $response->assertSessionHasErrors('amount');
    }

    /** @test */
    public function admin_can_mark_due_as_waived()
    {
        $this->actingAs($this->admin);

        $due = Due::factory()->create([
            'household_id' => $this->household->id,
            'status' => 'unpaid'
        ]);

        $response = $this->patch("/admin/dues/{$due->id}/waive", [
            'waive_reason' => 'Keluarga tidak mampu'
        ]);

        $response->assertRedirect('/admin/dues');

        $due->refresh();
        $this->assertEquals('waived', $due->status);
        $this->assertEquals('Keluarga tidak mampu', $due->waive_reason);
    }

    /** @test */
    public function admin_can_search_dues_by_household()
    {
        $this->actingAs($this->admin);

        $household = Household::factory()->create(['address' => 'Jalan Merdeka No. 123']);
        $due = Due::factory()->create(['household_id' => $household->id]);

        $response = $this->get('/admin/dues?search=Merdeka');

        $response->assertStatus(200);
        $response->assertSee('Merdeka');
    }
}
