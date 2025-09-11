<?php

namespace App\Services;

use App\Models\Letter;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function generateLetterPdf(Letter $letter)
    {
        // Generate letter number if not exists
        if (!$letter->number) {
            $letter->number = $this->generateLetterNumber($letter);
            $letter->save();
        }

        // Prepare data for PDF
        $data = [
            'letter' => $letter,
            'resident' => $letter->resident,
            'household' => $letter->resident->household,
            'generated_at' => now(),
            'letter_title' => $this->getLetterTitle($letter->type),
            'letter_body' => $this->getLetterBody($letter),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.letter', $data);

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Generate filename
        $filename = $this->generatePdfFilename($letter);

        // Save PDF to storage
        $pdfPath = 'letters/' . $filename;
        Storage::put($pdfPath, $pdf->output());

        // Update letter with PDF path
        $letter->update(['pdf_path' => $pdfPath]);

        return $pdfPath;
    }

    private function generateLetterNumber(Letter $letter)
    {
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $year = now()->year;

        // Count letters this month for sequential number
        $count = Letter::whereNotNull('number')
                      ->whereYear('created_at', $year)
                      ->whereMonth('created_at', now()->month)
                      ->count() + 1;

        $sequential = str_pad($count, 3, '0', STR_PAD_LEFT);

        // Format: 001/RT001/VIII/2025
        return sprintf('%s/RT001/%s/%s', $sequential, $this->getRomanMonth(), $year);
    }

    private function getRomanMonth()
    {
        $months = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        return $months[now()->month];
    }

    private function getLetterTitle($type)
    {
        $titles = [
            'domisili' => 'SURAT KETERANGAN DOMISILI',
            'pengantar' => 'SURAT PENGANTAR',
            'keterangan' => 'SURAT KETERANGAN'
        ];

        return $titles[$type] ?? 'SURAT KETERANGAN';
    }

    private function getLetterBody(Letter $letter)
    {
        $resident = $letter->resident;
        $household = $resident->household;

        switch ($letter->type) {
            case 'domisili':
                return "Yang bertanda tangan di bawah ini, Ketua RT 02 menerangkan bahwa:

Nama               : {$resident->nama}
NIK                : {$resident->nik}
Tempat/Tgl Lahir   : {$resident->tempat_lahir}, " . Carbon::parse($resident->tanggal_lahir)->format('d F Y') . "
Jenis Kelamin      : " . ($resident->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan') . "
Alamat             : {$household->alamat}

Adalah benar berdomisili di wilayah RT 02 dan tercatat sebagai warga dengan status aktif.

Surat keterangan ini dibuat untuk keperluan: {$letter->purpose}

Demikian surat keterangan ini dibuat dengan sebenarnya dan dapat dipergunakan sebagaimana mestinya.";

            case 'pengantar':
                return "Yang bertanda tangan di bawah ini, Ketua RT 02 dengan ini menerangkan bahwa:

Nama               : {$resident->nama}
NIK                : {$resident->nik}
Tempat/Tgl Lahir   : {$resident->tempat_lahir}, " . Carbon::parse($resident->tanggal_lahir)->format('d F Y') . "
Jenis Kelamin      : " . ($resident->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan') . "
Alamat             : {$household->alamat}

Adalah benar warga RT 02 yang berkelakuan baik dan tidak pernah terlibat dalam kegiatan yang melanggar hukum.

Surat pengantar ini dibuat untuk keperluan: {$letter->purpose}

Demikian surat pengantar ini kami buat dengan sebenarnya, atas perhatian dan kerjasamanya kami ucapkan terima kasih.";

            case 'keterangan':
                return "Yang bertanda tangan di bawah ini, Ketua RT 02 menerangkan dengan sesungguhnya bahwa:

Nama               : {$resident->nama}
NIK                : {$resident->nik}
Tempat/Tgl Lahir   : {$resident->tempat_lahir}, " . Carbon::parse($resident->tanggal_lahir)->format('d F Y') . "
Jenis Kelamin      : " . ($resident->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan') . "
Alamat             : {$household->alamat}

Surat keterangan ini dibuat untuk keperluan: {$letter->purpose}

Demikian surat keterangan ini dibuat dengan sebenarnya dan dapat dipergunakan seperlunya.";

            default:
                return "Surat keterangan untuk {$resident->nama} dengan keperluan: {$letter->purpose}";
        }
    }

    private function generatePdfFilename(Letter $letter)
    {
        $type = ucfirst($letter->type);
        $name = str_replace(' ', '_', $letter->resident->nama);
        $date = now()->format('Y-m-d');

        return "{$type}_{$name}_{$date}.pdf";
    }

    public function downloadPdf(Letter $letter)
    {
        if (!$letter->pdf_path || !Storage::exists($letter->pdf_path)) {
            // Generate PDF if not exists
            $this->generateLetterPdf($letter);
        }

        return Storage::download($letter->pdf_path);
    }
}
