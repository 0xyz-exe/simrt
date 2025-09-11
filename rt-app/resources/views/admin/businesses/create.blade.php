@extends('layouts.app')
@section('title', 'Tambah UMKM')
@section('content')
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('admin.businesses.index') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Kelola UMKM
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold">Tambah UMKM Baru</h1>
        <p class="text-slate-600">Daftarkan usaha mikro, kecil dan menengah warga RT</p>
    </div>

    <form method="POST" action="{{ route('admin.businesses.store') }}" enctype="multipart/form-data" class="max-w-4xl">
        @csrf

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Informasi Dasar -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Informasi Dasar</h3>

                    <!-- Nama Usaha -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Nama Usaha *</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200 @error('name') border-red-500 @enderror"
                               placeholder="Contoh: Warung Makan Bu Siti" required>
                        @error('name')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Pemilik -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Nama Pemilik *</label>
                        <input type="text" name="owner_name" value="{{ old('owner_name') }}"
                               class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200 @error('owner_name') border-red-500 @enderror"
                               placeholder="Nama lengkap pemilik" required>
                        @error('owner_name')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Pilih Resident (Opsional) -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Data Resident (Opsional)</label>
                        <select name="resident_id" class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200">
                            <option value="">-- Pilih jika ada di data resident --</option>
                            @foreach($residents as $resident)
                                <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                    {{ $resident->nama }} - {{ $resident->household->alamat ?? 'Alamat tidak tersedia' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-slate-500">Pilih jika pemilik sudah terdaftar sebagai resident</small>
                    </div>

                    <!-- Kategori -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Kategori Usaha *</label>
                        <select name="category" class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200 @error('category') border-red-500 @enderror" required>
                            <option value="">Pilih kategori</option>
                            @foreach(\App\Models\Business::getCategories() as $key => $label)
                                <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Deskripsi Usaha *</label>
                        <textarea name="description" rows="4"
                                  class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200 @error('description') border-red-500 @enderror"
                                  placeholder="Jelaskan usaha ini..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Status & Pengaturan -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Status & Pengaturan</h3>

                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="rounded">
                            <span class="ml-2">Status Aktif</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="featured" value="1"
                                   {{ old('featured') ? 'checked' : '' }}
                                   class="rounded">
                            <span class="ml-2">Jadikan Unggulan</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Kontak & Lokasi -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Kontak & Lokasi</h3>

                    <!-- Alamat -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Alamat</label>
                        <textarea name="address" rows="3"
                                  class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200"
                                  placeholder="Alamat lengkap usaha">{{ old('address') }}</textarea>
                    </div>

                    <!-- Telepon -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Nomor Telepon</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                               class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200"
                               placeholder="08xxxxxxxxxx">
                    </div>

                    <!-- WhatsApp -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">WhatsApp</label>
                        <input type="tel" name="whatsapp" value="{{ old('whatsapp') }}"
                               class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200"
                               placeholder="08xxxxxxxxxx">
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200"
                               placeholder="email@contoh.com">
                    </div>

                    <!-- Website -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Website</label>
                        <input type="url" name="website" value="{{ old('website') }}"
                               class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200"
                               placeholder="https://website.com">
                    </div>

                    <!-- Social Media -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Instagram</label>
                        <input type="text" name="instagram" value="{{ old('instagram') }}"
                               class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200"
                               placeholder="@username">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Facebook</label>
                        <input type="text" name="facebook" value="{{ old('facebook') }}"
                               class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200"
                               placeholder="Facebook Page/Profile">
                    </div>
                </div>

                <!-- Foto/Gambar -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Foto Usaha</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Upload Foto</label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200">
                        <small class="text-slate-500">Format: JPG, PNG, GIF. Maksimal 2MB</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex gap-4">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Simpan UMKM
            </button>
            <a href="{{ route('admin.businesses.index') }}"
               class="px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                Batal
            </a>
        </div>
    </form>

    <script>
        // Auto-fill owner name if resident is selected
        document.querySelector('select[name="resident_id"]').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const residentName = selectedOption.text.split(' - ')[0];
                document.querySelector('input[name="owner_name"]').value = residentName;
            }
        });
    </script>
@endsection
