@extends('layouts.app')
@section('title', 'Iuran Saya')
@section('content')
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold">Iuran Keluarga Saya</h1>
        <p class="text-slate-600">Riwayat iuran dan status pembayaran</p>
    </div>

    @if(isset($resident))
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="font-semibold mb-4">Informasi Keluarga</h3>
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm text-slate-500">Nama Kepala Keluarga</label>
                    <p class="font-semibold">{{ $resident->household->kepalaKeluarga?->nama ?? 'Belum ditentukan' }}</p>
                </div>
                <div>
                    <label class="text-sm text-slate-500">Nomor KK</label>
                    <p class="font-mono">{{ $resident->household->nomor_kk }}</p>
                </div>
                <div>
                    <label class="text-sm text-slate-500">Alamat</label>
                    <p>{{ $resident->household->alamat }}</p>
                </div>
            </div>
        </div>

        @if($dues->count() > 0)
            <!-- Summary Stats -->
            @php
                $totalDues = $dues->count();
                $paidDues = $dues->where('status', 'paid')->count();
                $unpaidDues = $totalDues - $paidDues;
                $totalAmount = $dues->sum('amount');
                $paidAmount = $dues->where('status', 'paid')->sum('amount');
                $unpaidAmount = $totalAmount - $paidAmount;
            @endphp

            <div class="grid md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm text-slate-500 mb-1">Total Iuran</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $totalDues }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm text-slate-500 mb-1">Sudah Bayar</h3>
                    <p class="text-2xl font-bold text-green-600">{{ $paidDues }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm text-slate-500 mb-1">Belum Bayar</h3>
                    <p class="text-2xl font-bold text-red-600">{{ $unpaidDues }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm text-slate-500 mb-1">Total Dibayar</h3>
                    <p class="text-lg font-bold text-purple-600">Rp {{ number_format($paidAmount, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Dues History -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="font-semibold">Riwayat Iuran</h3>
                </div>
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left py-3 px-4">Periode</th>
                            <th class="text-left py-3 px-4">Jumlah</th>
                            <th class="text-left py-3 px-4">Status</th>
                            <th class="text-left py-3 px-4">Tanggal Bayar</th>
                            <th class="text-left py-3 px-4">Metode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dues as $due)
                            <tr class="border-t">
                                <td class="py-3 px-4 font-semibold">
                                    {{ DateTime::createFromFormat('!m', $due->month)->format('M') }} {{ $due->year }}
                                </td>
                                <td class="py-3 px-4 font-semibold">
                                    Rp {{ number_format($due->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($due->status === 'paid')
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-sm rounded">
                                            ✓ Lunas
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-sm rounded">
                                            ✗ Belum Bayar
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($due->status === 'paid' && $due->payments->count() > 0)
                                        {{ $due->payments->first()->paid_at->format('d M Y') }}
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($due->status === 'paid' && $due->payments->count() > 0)
                                        @php
                                            $method = $due->payments->first()->method;
                                            $methodLabels = [
                                                'cash' => 'Tunai',
                                                'transfer' => 'Transfer',
                                                'qris' => 'QRIS'
                                            ];
                                        @endphp
                                        <span class="text-sm bg-slate-100 px-2 py-1 rounded">
                                            {{ $methodLabels[$method] ?? $method }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Outstanding Dues Alert -->
            @if($unpaidDues > 0)
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="text-yellow-600 mr-3">⚠️</div>
                        <div>
                            <h3 class="font-semibold text-yellow-800">Reminder Pembayaran</h3>
                            <p class="text-yellow-700 mt-1">
                                Anda memiliki {{ $unpaidDues }} iuran yang belum dibayar dengan total
                                <strong>Rp {{ number_format($unpaidAmount, 0, ',', '.') }}</strong>.
                                Silakan hubungi bendahara RT untuk melakukan pembayaran.
                            </p>
                            <div class="mt-3 text-sm text-yellow-600">
                                <p><strong>Info Pembayaran:</strong></p>
                                <p>• Bendahara RT: Pak/Bu [Nama Bendahara]</p>
                                <p>• Waktu: Senin-Jumat, 19.00-21.00 WIB</p>
                                <p>• Transfer: Bank BCA 1234567890 a.n. RT 05/RW 02</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-12 bg-white rounded-lg shadow">
                <p class="text-slate-500 mb-4">Belum ada data iuran untuk keluarga Anda</p>
                <p class="text-sm text-slate-400">Data iuran akan muncul setelah pengurus RT membuat iuran bulanan</p>
            </div>
        @endif
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <div class="text-slate-500">
                <p class="text-lg mb-2">Data Keluarga Tidak Ditemukan</p>
                <p class="mb-4">Sepertinya data Anda belum terdaftar dalam sistem RT atau email yang digunakan tidak sesuai dengan data warga.</p>
                <p class="text-sm">Silakan hubungi pengurus RT untuk memastikan data Anda sudah terdaftar dengan benar.</p>
            </div>
        </div>
    @endif
@endsection
