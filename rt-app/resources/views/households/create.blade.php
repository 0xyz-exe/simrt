@extends('layouts.app')
@section('title', 'Tambah Keluarga')
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Tambah Data Keluarga</h1>
        <p class="text-slate-600">Tambahkan data keluarga baru ke sistem RT</p>
    </div>

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
        <form method="POST" action="{{ route('households.store') }}" class="space-y-6">
            @csrf

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Nomor KK *</label>
                    <input name="nomor_kk" type="text" required
                           value="{{ old('nomor_kk') }}"
                           class="w-full border rounded px-3 py-2 focus:border-blue-500 focus:outline-none"
                           placeholder="Contoh: 3201012345678901">
                    @error('nomor_kk')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-semibold mb-2">RT *</label>
                        <input name="rt" type="text" required maxlength="4"
                               value="{{ old('rt') }}"
                               class="w-full border rounded px-3 py-2 focus:border-blue-500 focus:outline-none"
                               placeholder="05">
                        @error('rt')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">RW *</label>
                        <input name="rw" type="text" required maxlength="4"
                               value="{{ old('rw') }}"
                               class="w-full border rounded px-3 py-2 focus:border-blue-500 focus:outline-none"
                               placeholder="02">
                        @error('rw')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Alamat Lengkap *</label>
                <textarea name="alamat" required rows="3"
                          class="w-full border rounded px-3 py-2 focus:border-blue-500 focus:outline-none"
                          placeholder="Jl. Contoh No. 123, Kelurahan ABC, Kecamatan XYZ">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Simpan
                </button>
                <a href="{{ route('households.index') }}"
                   class="px-6 py-2 border rounded hover:bg-slate-50">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <div class="max-w-2xl mx-auto mt-6 bg-blue-50 p-4 rounded-lg">
        <h3 class="font-semibold text-blue-800 mb-2">💡 Tips:</h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• Setelah menambah keluarga, jangan lupa tambahkan anggota keluarga di menu "Data Warga"</li>
            <li>• Nomor KK harus unik dan sesuai dengan dokumen resmi</li>
            <li>• RT/RW diisi sesuai wilayah tempat tinggal</li>
        </ul>
    </div>
@endsection
