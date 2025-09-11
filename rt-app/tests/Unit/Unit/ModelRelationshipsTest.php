<?php

namespace Tests\Unit\Unit;

use Tests\TestCase;
use App\Models\{Household, Resident, Due, Payment, Letter, AuditLog};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function household_has_many_residents()
    {
        $household = Household::factory()->create();
        $residents = Resident::factory()->count(3)->create([
            'household_id' => $household->id
        ]);

        $this->assertCount(3, $household->residents);
        $this->assertInstanceOf(Resident::class, $household->residents->first());
    }

    /** @test */
    public function household_has_many_dues()
    {
        $household = Household::factory()->create();
        $dues = [];
        for ($i = 1; $i <= 3; $i++) {
            $dues[] = Due::factory()->create([
                'household_id' => $household->id,
                'month' => $i,
                'year' => 2024
            ]);
        }

        $this->assertCount(3, $household->dues);
        $this->assertInstanceOf(Due::class, $household->dues->first());
    }

    /** @test */
    public function resident_belongs_to_household()
    {
        $household = Household::factory()->create();
        $resident = Resident::factory()->create([
            'household_id' => $household->id
        ]);

        $this->assertInstanceOf(Household::class, $resident->household);
        $this->assertEquals($household->id, $resident->household->id);
    }

    /** @test */
    public function resident_has_many_letters()
    {
        $household = Household::factory()->create();
        $resident = Resident::factory()->create([
            'household_id' => $household->id
        ]);
        $letters = Letter::factory()->count(3)->create([
            'resident_id' => $resident->id
        ]);

        $this->assertCount(3, $resident->letters);
        $this->assertInstanceOf(Letter::class, $resident->letters->first());
    }

    /** @test */
    public function due_belongs_to_household()
    {
        $household = Household::factory()->create();
        $due = Due::factory()->create([
            'household_id' => $household->id,
            'month' => 1,
            'year' => 2024
        ]);

        $this->assertInstanceOf(Household::class, $due->household);
        $this->assertEquals($household->id, $due->household->id);
    }

    /** @test */
    public function due_has_many_payments()
    {
        $household = Household::factory()->create();
        $due = Due::factory()->create([
            'household_id' => $household->id,
            'month' => 1,
            'year' => 2024
        ]);
        $payments = Payment::factory()->count(2)->create([
            'due_id' => $due->id
        ]);

        $this->assertCount(2, $due->payments);
        $this->assertInstanceOf(Payment::class, $due->payments->first());
    }

    /** @test */
    public function payment_belongs_to_due()
    {
        $household = Household::factory()->create();
        $due = Due::factory()->create([
            'household_id' => $household->id,
            'month' => 1,
            'year' => 2024
        ]);
        $payment = Payment::factory()->create([
            'due_id' => $due->id
        ]);

        $this->assertInstanceOf(Due::class, $payment->due);
        $this->assertEquals($due->id, $payment->due->id);
    }

    /** @test */
    public function letter_belongs_to_resident()
    {
        $household = Household::factory()->create();
        $resident = Resident::factory()->create([
            'household_id' => $household->id
        ]);
        $letter = Letter::factory()->create([
            'resident_id' => $resident->id
        ]);

        $this->assertInstanceOf(Resident::class, $letter->resident);
        $this->assertEquals($resident->id, $letter->resident->id);
    }

    /** @test */
    public function model_factories_create_valid_data()
    {
        $household = Household::factory()->create();
        $resident = Resident::factory()->create();
        $due = Due::factory()->create();
        $payment = Payment::factory()->create();
        $letter = Letter::factory()->create();

        $this->assertDatabaseHas('households', ['id' => $household->id]);
        $this->assertDatabaseHas('residents', ['id' => $resident->id]);
        $this->assertDatabaseHas('dues', ['id' => $due->id]);
        $this->assertDatabaseHas('payments', ['id' => $payment->id]);
        $this->assertDatabaseHas('letters', ['id' => $letter->id]);
    }
}
