@extends('layouts.app')
@section('title', 'Ajukan Surat')
@section('content')
    <div class="max-w-2xl mx-auto">
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
            <h1 class="text-2xl font-bold">Ajukan Surat</h1>
            <p class="text-slate-600">Ajukan surat domisili, pengantar, atau keterangan</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('letters.store') }}">
                @csrf

                <div class="mb-6">
                    <label for="type" class="block text-sm font-medium text-slate-700 mb-2">Jenis Surat</label>
                    <select name="type" id="type" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Pilih jenis surat</option>
                        <option value="domisili" {{ old('type') === 'domisili' ? 'selected' : '' }}>Surat Keterangan Domisili</option>
                        <option value="pengantar" {{ old('type') === 'pengantar' ? 'selected' : '' }}>Surat Pengantar</option>
                        <option value="keterangan" {{ old('type') === 'keterangan' ? 'selected' : '' }}>Surat Keterangan</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="purpose" class="block text-sm font-medium text-slate-700 mb-2">Keperluan</label>
                    <input type="text" name="purpose" id="purpose" value="{{ old('purpose') }}" required
                           placeholder="Contoh: Untuk keperluan melamar kerja"
                           class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('purpose')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-slate-700 mb-2">Catatan Tambahan (opsional)</label>
                    <textarea name="notes" id="notes" rows="3"
                              placeholder="Catatan atau keterangan tambahan"
                              class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preview Info -->
                <div class="mb-6 p-4 bg-slate-50 rounded-lg">
                    <h4 class="font-medium text-slate-700 mb-2">Informasi Pemohon:</h4>
                    @if(auth()->user()->resident)
                        <div class="text-sm text-slate-600">
                            <p><strong>Nama:</strong> {{ auth()->user()->resident->nama }}</p>
                            <p><strong>NIK:</strong> {{ auth()->user()->resident->nik }}</p>
                            <p><strong>Alamat:</strong> {{ auth()->user()->resident->household->alamat }}</p>
                        </div>
                    @else
                        <div class="text-sm text-red-600">
                            ⚠️ Anda belum terdaftar sebagai penduduk. Silakan hubungi admin RT.
                        </div>
                    @endif
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                            @if(!auth()->user()->resident) disabled @endif>
                        Ajukan Surat
                    </button>
                    <a href="{{ route('dashboard') }}"
                       class="px-6 py-2 border border-slate-300 text-slate-700 rounded-md hover:bg-slate-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="mt-6 p-4 bg-green-50 rounded-lg">
            <h4 class="font-medium text-green-800 mb-2">📋 Informasi</h4>
            <ul class="text-sm text-green-700 space-y-1">
                <li>• Pengajuan akan diproses oleh admin RT dalam 1-3 hari kerja</li>
                <li>• Pastikan data keperluan sudah benar sebelum mengajukan</li>
                <li>• Anda akan mendapat notifikasi setelah surat disetujui</li>
                <li>• Surat yang disetujui dapat didownload melalui menu "Riwayat Surat"</li>
            </ul>
        </div>
    </div>
@endsection
