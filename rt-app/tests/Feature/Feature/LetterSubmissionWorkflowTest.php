<?php

namespace Tests\Feature\Feature;

use Tests\TestCase;
use App\Models\{User, Household, Resident, Letter};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Notification, Storage};
use App\Notifications\LetterStatusNotification;

class LetterSubmissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $resident;
    protected $household;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'admin'
        ]);

        // Create household and resident
        $this->household = Household::factory()->create();
        $this->resident = Resident::factory()->create([
            'household_id' => $this->household->id
        ]);
    }

    /** @test */
    public function admin_can_view_letters_dashboard()
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/letters');

        $response->assertStatus(200);
        $response->assertViewIs('admin.letters.index');
        $response->assertSee('Manajemen Surat');
    }

    /** @test */
    public function admin_can_create_new_letter()
    {
        $this->actingAs($this->admin);

        $letterData = [
            'resident_id' => $this->resident->id,
            'type' => 'domisili',
            'purpose' => 'Untuk keperluan administrasi',
            'status' => 'draft'
        ];

        $response = $this->post('/admin/letters', $letterData);

        $response->assertRedirect('/admin/letters');
        $this->assertDatabaseHas('letters', $letterData);
    }

    /** @test */
    public function admin_can_approve_letter_and_generate_pdf()
    {
        Notification::fake();
        Storage::fake('public');

        $this->actingAs($this->admin);

        $letter = Letter::factory()->create([
            'resident_id' => $this->resident->id,
            'type' => 'domisili',
            'status' => 'pending'
        ]);

        $response = $this->patch("/admin/letters/{$letter->id}", [
            'status' => 'approved'
        ]);

        $response->assertRedirect('/admin/letters');

        $letter->refresh();
        $this->assertEquals('approved', $letter->status);
        $this->assertNotNull($letter->letter_number);

        // Check if notification was sent
        Notification::assertSentTo(
            [$this->resident],
            LetterStatusNotification::class
        );
    }

    /** @test */
    public function admin_can_reject_letter_with_reason()
    {
        Notification::fake();

        $this->actingAs($this->admin);

        $letter = Letter::factory()->create([
            'resident_id' => $this->resident->id,
            'status' => 'pending'
        ]);

        $response = $this->patch("/admin/letters/{$letter->id}", [
            'status' => 'rejected',
            'remarks' => 'Data tidak lengkap'
        ]);

        $response->assertRedirect('/admin/letters');

        $letter->refresh();
        $this->assertEquals('rejected', $letter->status);
        $this->assertEquals('Data tidak lengkap', $letter->remarks);

        Notification::assertSentTo(
            [$this->resident],
            LetterStatusNotification::class
        );
    }

    /** @test */
    public function admin_can_view_letter_details()
    {
        $this->actingAs($this->admin);

        $letter = Letter::factory()->create([
            'resident_id' => $this->resident->id,
            'type' => 'domisili',
            'purpose' => 'Untuk keperluan administrasi'
        ]);

        $response = $this->get("/admin/letters/{$letter->id}");

        $response->assertStatus(200);
        $response->assertViewIs('admin.letters.show');
        $response->assertSee($letter->purpose);
        $response->assertSee($this->resident->name);
    }

    /** @test */
    public function admin_can_filter_letters_by_status()
    {
        $this->actingAs($this->admin);

        // Create letters with different statuses
        Letter::factory()->create(['status' => 'pending', 'resident_id' => $this->resident->id]);
        Letter::factory()->create(['status' => 'approved', 'resident_id' => $this->resident->id]);
        Letter::factory()->create(['status' => 'rejected', 'resident_id' => $this->resident->id]);

        $response = $this->get('/admin/letters?status=pending');

        $response->assertStatus(200);
        $response->assertSee('pending');
    }

    /** @test */
    public function letter_numbering_system_works_correctly()
    {
        $this->actingAs($this->admin);

        // Create and approve multiple letters
        $letter1 = Letter::factory()->create([
            'resident_id' => $this->resident->id,
            'type' => 'domisili',
            'status' => 'pending'
        ]);

        $letter2 = Letter::factory()->create([
            'resident_id' => $this->resident->id,
            'type' => 'keterangan',
            'status' => 'pending'
        ]);

        // Approve first letter
        $this->patch("/admin/letters/{$letter1->id}", ['status' => 'approved']);
        $letter1->refresh();

        // Approve second letter
        $this->patch("/admin/letters/{$letter2->id}", ['status' => 'approved']);
        $letter2->refresh();

        $this->assertNotNull($letter1->letter_number);
        $this->assertNotNull($letter2->letter_number);
        $this->assertNotEquals($letter1->letter_number, $letter2->letter_number);
    }

    /** @test */
    public function admin_can_bulk_approve_letters()
    {
        Notification::fake();

        $this->actingAs($this->admin);

        $letters = Letter::factory()->count(3)->create([
            'resident_id' => $this->resident->id,
            'status' => 'pending'
        ]);

        $letterIds = $letters->pluck('id')->toArray();

        $response = $this->post('/admin/letters/bulk-approve', [
            'letter_ids' => $letterIds
        ]);

        $response->assertRedirect('/admin/letters');

        foreach ($letters as $letter) {
            $letter->refresh();
            $this->assertEquals('approved', $letter->status);
            $this->assertNotNull($letter->letter_number);
        }
    }

    /** @test */
    public function admin_can_search_letters()
    {
        $this->actingAs($this->admin);

        $letter = Letter::factory()->create([
            'resident_id' => $this->resident->id,
            'purpose' => 'Untuk keperluan khusus administrasi'
        ]);

        $response = $this->get('/admin/letters?search=khusus');

        $response->assertStatus(200);
        $response->assertSee($letter->purpose);
    }
}
