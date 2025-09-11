@extends('layouts.app')
@section('title', 'Edit Warga')
@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Edit Data Warga</h1>
                <p class="text-slate-600">Perbarui informasi data warga</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('residents.show', $resident) }}"
                   class="px-4 py-2 border border-slate-300 text-slate-700 rounded hover:bg-slate-50">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('residents.update', $resident) }}">
            @csrf
            @method('PUT')

            <div class="grid md:grid-cols-2 gap-6">
                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700 mb-2">
                        NIK <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="nik"
                           name="nik"
                           value="{{ old('nik', $resident->nik) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nik') border-red-500 @enderror"
                           placeholder="Masukkan NIK (16 digit)"
                           maxlength="16"
                           required>
                    @error('nik')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama -->
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="nama"
                           name="nama"
                           value="{{ old('nama', $resident->nama) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama') border-red-500 @enderror"
                           placeholder="Masukkan nama lengkap"
                           required>
                    @error('nama')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-2">
                        Tempat Lahir
                    </label>
                    <input type="text"
                           id="tempat_lahir"
                           name="tempat_lahir"
                           value="{{ old('tempat_lahir', $resident->tempat_lahir) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tempat_lahir') border-red-500 @enderror"
                           placeholder="Masukkan tempat lahir">
                    @error('tempat_lahir')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Lahir
                    </label>
                    <input type="date"
                           id="tanggal_lahir"
                           name="tanggal_lahir"
                           value="{{ old('tanggal_lahir', $resident->tanggal_lahir ? $resident->tanggal_lahir->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_lahir') border-red-500 @enderror">
                    @error('tanggal_lahir')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Kelamin
                    </label>
                    <select id="jenis_kelamin"
                            name="jenis_kelamin"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jenis_kelamin') border-red-500 @enderror">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('jenis_kelamin', $resident->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $resident->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No. Telepon -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        No. Telepon
                    </label>
                    <input type="text"
                           id="phone"
                           name="phone"
                           value="{{ old('phone', $resident->phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                           placeholder="Masukkan nomor telepon">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email', $resident->email) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="Masukkan alamat email">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Household Selection -->
                <div>
                    <label for="household_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Keluarga <span class="text-red-500">*</span>
                    </label>
                    <select id="household_id"
                            name="household_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('household_id') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Keluarga</option>
                        @foreach($households as $household)
                            <option value="{{ $household->id }}"
                                {{ old('household_id', $resident->household_id) == $household->id ? 'selected' : '' }}>
                                {{ $household->nomor_kk }} - {{ $household->alamat }}
                            </option>
                        @endforeach
                    </select>
                    @error('household_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kepala Keluarga -->
            <div class="mt-6">
                <div class="flex items-center">
                    <input type="checkbox"
                           id="is_head"
                           name="is_head"
                           value="1"
                           {{ old('is_head', $resident->is_head) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_head" class="ml-2 block text-sm text-gray-900">
                        Kepala Keluarga
                    </label>
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    Centang jika warga ini adalah kepala keluarga
                </p>
                @error('is_head')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Data Display -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Data Saat Ini:</h3>
                <div class="text-sm text-gray-600 space-y-1">
                    <p><strong>NIK:</strong> {{ $resident->nik }}</p>
                    <p><strong>Nama:</strong> {{ $resident->nama }}</p>
                    <p><strong>Keluarga:</strong> {{ $resident->household->nomor_kk }} - {{ $resident->household->alamat }}</p>
                    @if($resident->is_head)
                        <p><strong>Status:</strong> <span class="text-green-600">Kepala Keluarga</span></p>
                    @endif
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 pt-6 mt-6 border-t">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Simpan Perubahan
                </button>
                <a href="{{ route('residents.show', $resident) }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        // Auto-format NIK input
        document.getElementById('nik').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 16) {
                value = value.substring(0, 16);
            }
            e.target.value = value;
        });

        // Format phone number
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });

        // Warn about head of household change
        document.getElementById('is_head').addEventListener('change', function(e) {
            if (e.target.checked) {
                alert('Perhatian: Menandai warga ini sebagai kepala keluarga akan membatalkan status kepala keluarga dari warga lain dalam keluarga yang sama.');
            }
        });
    </script>
@endsection
