<?php

namespace Tests\Unit\Unit;

use Tests\TestCase;
use App\Services\PdfService;
use App\Models\{Letter, Resident, Household, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class PdfServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $pdfService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdfService = new PdfService();
        Storage::fake('local');
    }

    /** @test */
    public function it_can_generate_letter_number()
    {
        // Create a letter without number
        $letter = Letter::factory()->create(['number' => null]);

        $this->pdfService->generateLetterPdf($letter);

        $letter->refresh();
        $this->assertNotNull($letter->number);
        $this->assertMatchesRegularExpression('/\d{3}\/RT001\/[IVX]+\/\d{4}/', $letter->number);
    }

    /** @test */
    public function it_generates_pdf_file_successfully()
    {
        $household = Household::factory()->create();
        $resident = Resident::factory()->create(['household_id' => $household->id]);
        $letter = Letter::factory()->create(['resident_id' => $resident->id]);

        $pdfPath = $this->pdfService->generateLetterPdf($letter);

        $this->assertNotNull($pdfPath);
        Storage::assertExists($pdfPath);

        $letter->refresh();
        $this->assertEquals($pdfPath, $letter->pdf_path);
    }

    /** @test */
    public function it_handles_different_letter_types()
    {
        $types = ['domisili', 'pengantar', 'keterangan'];

        foreach ($types as $type) {
            $household = Household::factory()->create();
            $resident = Resident::factory()->create(['household_id' => $household->id]);
            $letter = Letter::factory()->create([
                'resident_id' => $resident->id,
                'type' => $type
            ]);

            $pdfPath = $this->pdfService->generateLetterPdf($letter);

            $this->assertNotNull($pdfPath);
            Storage::assertExists($pdfPath);
        }
    }

    /** @test */
    public function it_can_download_existing_pdf()
    {
        $household = Household::factory()->create();
        $resident = Resident::factory()->create(['household_id' => $household->id]);
        $letter = Letter::factory()->create([
            'resident_id' => $resident->id,
            'pdf_path' => 'letters/test.pdf'
        ]);

        // Create fake PDF file
        Storage::put('letters/test.pdf', 'fake pdf content');

        $response = $this->pdfService->downloadPdf($letter);

        $this->assertNotNull($response);
    }

    /** @test */
    public function it_generates_pdf_if_not_exists_on_download()
    {
        $household = Household::factory()->create();
        $resident = Resident::factory()->create(['household_id' => $household->id]);
        $letter = Letter::factory()->create([
            'resident_id' => $resident->id,
            'pdf_path' => null
        ]);

        $response = $this->pdfService->downloadPdf($letter);

        $letter->refresh();
        $this->assertNotNull($letter->pdf_path);
        Storage::assertExists($letter->pdf_path);
    }
}
