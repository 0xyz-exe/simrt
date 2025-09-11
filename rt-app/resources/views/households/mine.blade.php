@extends('layouts.app')
@section('title', 'Data Keluarga Saya')
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
        <h1 class="text-2xl font-bold">Data Keluarga Saya</h1>
        <p class="text-slate-600">Informasi keluarga yang terdaftar di sistem RT</p>
    </div>

    @if(isset($household))
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Info Keluarga -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-semibold text-lg mb-4">Informasi Keluarga</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-slate-500">Nomor KK</label>
                            <p class="font-mono">{{ $household->nomor_kk }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">RT/RW</label>
                            <p>{{ $household->rt }}/{{ $household->rw }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Alamat</label>
                            <p>{{ $household->alamat }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Kepala Keluarga</label>
                            @if($household->kepalaKeluarga)
                                <p class="font-semibold">{{ $household->kepalaKeluarga->nama }}</p>
                                <p class="text-sm text-slate-600">{{ $household->kepalaKeluarga->nik }}</p>
                            @else
                                <p class="text-slate-400">Belum ditentukan</p>
                            @endif
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Jumlah Anggota</label>
                            <p>{{ $household->residents->count() }} orang</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Anggota Keluarga -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b">
                        <h3 class="font-semibold text-lg">Anggota Keluarga</h3>
                    </div>

                    @if($household->residents->count() > 0)
                        <div class="divide-y">
                            @foreach($household->residents as $resident)
                                <div class="p-4">
                                    <div>
                                        <h4 class="font-semibold">
                                            {{ $resident->nama }}
                                            @if($resident->is_head)
                                                <span class="inline-block ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Kepala Keluarga</span>
                                            @endif
                                        </h4>
                                        <p class="text-sm text-slate-600 font-mono">NIK: {{ $resident->nik }}</p>
                                        @if($resident->phone)
                                            <p class="text-sm text-slate-600">📞 {{ $resident->phone }}</p>
                                        @endif
                                        @if($resident->email)
                                            <p class="text-sm text-slate-600">📧 {{ $resident->email }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center text-slate-500">
                            <p>Belum ada anggota keluarga tercatat</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Riwayat Iuran (jika ada) -->
        @if($household->dues->count() > 0)
            <div class="mt-6 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b">
                    <h3 class="font-semibold text-lg">Riwayat Iuran Keluarga</h3>
                </div>
                <div class="p-6">
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($household->dues->sortByDesc('year')->sortByDesc('month') as $due)
                            <div class="border rounded p-3">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-semibold">
                                        {{ DateTime::createFromFormat('!m', $due->month)->format('M') }} {{ $due->year }}
                                    </span>
                                    <span class="px-2 py-1 text-xs rounded
                                        {{ $due->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $due->status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                                    </span>
                                </div>
                                <p class="text-sm text-slate-600">Rp {{ number_format($due->amount, 0, ',', '.') }}</p>
                                @if($due->payments->count() > 0)
                                    <p class="text-xs text-slate-500 mt-1">
                                        Dibayar: {{ $due->payments->first()->paid_at->format('d M Y') }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
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
