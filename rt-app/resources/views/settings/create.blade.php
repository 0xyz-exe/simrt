@extends('layouts.app')
@section('title', 'Tambah Pengaturan')
@section('content')
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('settings.index') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Pengaturan
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold">Tambah Pengaturan</h1>
        <p class="text-slate-600">Tambahkan pengaturan baru untuk sistem</p>
    </div>

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
        <form method="POST" action="{{ route('settings.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Key -->
                <div>
                    <label for="key" class="block text-sm font-medium text-gray-700 mb-2">
                        Key/Kunci *
                    </label>
                    <input type="text"
                           id="key"
                           name="key"
                           value="{{ old('key') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('key') border-red-500 @enderror"
                           placeholder="contoh: site_name"
                           required>
                    @error('key')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Label -->
                <div>
                    <label for="label" class="block text-sm font-medium text-gray-700 mb-2">
                        Label *
                    </label>
                    <input type="text"
                           id="label"
                           name="label"
                           value="{{ old('label') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('label') border-red-500 @enderror"
                           placeholder="Nama Situs"
                           required>
                    @error('label')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe *
                    </label>
                    <select id="type"
                            name="type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Tipe</option>
                        <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Text</option>
                        <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Number</option>
                        <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>Boolean</option>
                        <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>Image</option>
                        <option value="rich_text" {{ old('type') == 'rich_text' ? 'selected' : '' }}>Rich Text</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Group -->
                <div>
                    <label for="group" class="block text-sm font-medium text-gray-700 mb-2">
                        Grup *
                    </label>
                    <input type="text"
                           id="group"
                           name="group"
                           value="{{ old('group') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('group') border-red-500 @enderror"
                           placeholder="general, contact, etc"
                           required>
                    @error('group')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Value -->
            <div>
                <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                    Nilai
                </label>
                <div id="value-container">
                    <!-- Text Input -->
                    <input type="text"
                           id="value-text"
                           name="value"
                           value="{{ old('value') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent value-input"
                           placeholder="Masukkan nilai">

                    <!-- Number Input -->
                    <input type="number"
                           id="value-number"
                           name="value"
                           value="{{ old('value') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent value-input"
                           placeholder="Masukkan angka"
                           style="display: none;">

                    <!-- Boolean Input -->
                    <div id="value-boolean" class="value-input" style="display: none;">
                        <label class="flex items-center">
                            <input type="checkbox" name="value" value="1" class="mr-2">
                            <span>Aktif</span>
                        </label>
                    </div>

                    <!-- Image Input -->
                    <input type="file"
                           id="value-image"
                           name="value"
                           accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent value-input"
                           style="display: none;">

                    <!-- Rich Text Input -->
                    <textarea id="value-rich-text"
                              name="value"
                              rows="5"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent value-input"
                              placeholder="Masukkan teks lengkap"
                              style="display: none;">{{ old('value') }}</textarea>
                </div>
                @error('value')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea id="description"
                          name="description"
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                          placeholder="Deskripsi pengaturan">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                        Urutan Tampil
                    </label>
                    <input type="number"
                           id="sort_order"
                           name="sort_order"
                           value="{{ old('sort_order', 0) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('sort_order') border-red-500 @enderror"
                           min="0">
                    @error('sort_order')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Public -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="is_public"
                               value="1"
                               {{ old('is_public') ? 'checked' : '' }}
                               class="mr-2">
                        <span>Tampilkan di publik</span>
                    </label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 pt-6 mt-6 border-t">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Simpan Pengaturan
                </button>
                <a href="{{ route('settings.index') }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        // Show/hide value input based on type
        document.getElementById('type').addEventListener('change', function() {
            const type = this.value;
            const inputs = document.querySelectorAll('.value-input');

            // Hide all value inputs
            inputs.forEach(input => {
                input.style.display = 'none';
                input.disabled = true;
            });

            // Show selected type input
            if (type) {
                const selectedInput = document.getElementById(`value-${type.replace('_', '-')}`);
                if (selectedInput) {
                    selectedInput.style.display = 'block';
                    selectedInput.disabled = false;
                }
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            if (typeSelect.value) {
                typeSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection
