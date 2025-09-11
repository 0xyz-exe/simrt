<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $letter_title }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 10pt;
        }

        .letter-title {
            text-align: center;
            margin: 30px 0;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .letter-number {
            text-align: center;
            margin-bottom: 30px;
            font-size: 12pt;
        }

        .letter-body {
            text-align: justify;
            margin: 20px 0;
            white-space: pre-line;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
            width: 100%;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            width: 200px;
        }

        .signature-date {
            margin-bottom: 10px;
        }

        .signature-title {
            margin-bottom: 60px;
            font-weight: bold;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .footer {
            margin-top: 40px;
            font-size: 10pt;
            color: #666;
        }

        .stamp-area {
            width: 150px;
            height: 150px;
            border: 2px dashed #ccc;
            display: inline-block;
            vertical-align: top;
            margin-left: 50px;
            text-align: center;
            line-height: 150px;
            color: #999;
            font-size: 10pt;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>RUKUN TETANGGA 001</h1>
        <h2>KELURAHAN EXAMPLE</h2>
        <p>Alamat: Jl. Contoh No. 123, Kota Example 12345</p>
        <p>Telepon: (021) 1234567 | Email: rt001@example.com</p>
    </div>

    <!-- Letter Title -->
    <div class="letter-title">
        {{ $letter_title }}
    </div>

    <!-- Letter Number -->
    <div class="letter-number">
        Nomor: {{ $letter->number }}
    </div>

    <!-- Letter Body -->
    <div class="letter-body">
        {{ $letter_body }}

        @if($letter->notes)

        Catatan: {{ $letter->notes }}
        @endif
    </div>

    <!-- Signature Section -->
    <div class="signature">
        <div class="signature-box">
            <div class="signature-date">
                {{ $household->alamat }}, {{ $generated_at->format('d F Y') }}
            </div>
            <div class="signature-title">
                Ketua RT 02
            </div>
            <div class="signature-name">
                NAMA KETUA RT
            </div>
        </div>

        <!-- Stamp Area -->
        <div class="stamp-area">
            STEMPEL<br>RT 02
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Catatan:</strong></p>
        <ul style="margin: 5px 0; padding-left: 20px;">
            <li>Surat ini berlaku untuk keperluan yang tercantum di atas</li>
            <li>Apabila dikemudian hari terdapat kekeliruan, akan diperbaiki sebagaimana mestinya</li>
            <li>Surat ini digenerate secara otomatis pada {{ $generated_at->format('d F Y H:i') }}</li>
        </ul>
    </div>
</body>
</html>
