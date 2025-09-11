@extends('layouts.app')
@section('title', 'Edit Data Keluarga')
@section('content')
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('households.show', $household) }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Detail Keluarga
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold">Edit Data Keluarga</h1>
        <p class="text-slate-600">Edit informasi keluarga di RT</p>
    </div>

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
        <form method="POST" action="{{ route('households.update', $household) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nomor KK -->
            <div>
                <label for="nomor_kk" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Kartu Keluarga *
                </label>
                <input type="text"
                       id="nomor_kk"
                       name="nomor_kk"
                       value="{{ old('nomor_kk', $household->nomor_kk) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_kk') border-red-500 @enderror"
                       placeholder="Masukkan nomor KK (16 digit)"
                       maxlength="16"
                       required>
                @error('nomor_kk')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Alamat -->
            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                    Alamat Lengkap *
                </label>
                <textarea id="alamat"
                          name="alamat"
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('alamat') border-red-500 @enderror"
                          placeholder="Masukkan alamat lengkap"
                          required>{{ old('alamat', $household->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- RT -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700 mb-2">
                        RT *
                    </label>
                    <input type="text"
                           id="rt"
                           name="rt"
                           value="{{ old('rt', $household->rt) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rt') border-red-500 @enderror"
                           placeholder="Contoh: 001"
                           maxlength="4"
                           required>
                    @error('rt')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- RW -->
                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700 mb-2">
                        RW *
                    </label>
                    <input type="text"
                           id="rw"
                           name="rw"
                           value="{{ old('rw', $household->rw) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rw') border-red-500 @enderror"
                           placeholder="Contoh: 002"
                           maxlength="4"
                           required>
                    @error('rw')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 pt-6 mt-6 border-t">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Simpan Perubahan
                </button>
                <a href="{{ route('households.show', $household) }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        // Auto-format nomor KK input
        document.getElementById('nomor_kk').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 16) {
                value = value.substring(0, 16);
            }
            e.target.value = value;
        });

        // Format RT and RW with leading zeros
        ['rt', 'rw'].forEach(function(fieldId) {
            document.getElementById(fieldId).addEventListener('blur', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 0) {
                    e.target.value = value.padStart(3, '0');
                }
            });
        });
    </script>
@endsection
