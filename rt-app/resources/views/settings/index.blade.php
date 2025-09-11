@extends('layouts.app')
@section('title', 'Pengaturan Website')
@section('content')
    <!-- Back to Dashboard Button -->
    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Pengaturan Website</h1>
            <p class="text-slate-600">Kelola konten dan pengaturan sistem RT</p>
        </div>
        <a href="{{ route('settings.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Tambah Pengaturan
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        @foreach($settingGroups as $groupName => $settings)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6 capitalize">
                    {{ ucfirst(str_replace('_', ' ', $groupName)) }}
                </h2>

                <div class="grid gap-6">
                    @foreach($settings as $setting)
                        <div class="grid md:grid-cols-3 gap-4 items-start">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ $setting->label }}
                                </label>
                                @if($setting->description)
                                    <p class="text-xs text-gray-500">{{ $setting->description }}</p>
                                @endif
                            </div>

                            <div class="md:col-span-2">
                                @switch($setting->type)
                                    @case('text')
                                        <input type="text"
                                               name="{{ $setting->key }}"
                                               value="{{ $setting->value }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @break

                                    @case('number')
                                        <input type="number"
                                               name="{{ $setting->key }}"
                                               value="{{ $setting->value }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @break

                                    @case('boolean')
                                        <label class="flex items-center">
                                            <input type="checkbox"
                                                   name="{{ $setting->key }}"
                                                   value="1"
                                                   {{ $setting->value ? 'checked' : '' }}
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-600">Aktifkan</span>
                                        </label>
                                        @break

                                    @case('image')
                                        <div class="space-y-2">
                                            @if($setting->value)
                                                <div class="mb-2">
                                                    <img src="{{ Storage::url($setting->value) }}"
                                                         alt="{{ $setting->label }}"
                                                         class="w-32 h-32 object-cover rounded-lg">
                                                </div>
                                            @endif
                                            <input type="file"
                                                   name="{{ $setting->key }}"
                                                   accept="image/*"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        @break

                                    @case('rich_text')
                                        <textarea name="{{ $setting->key }}"
                                                  rows="4"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $setting->value }}</textarea>
                                        @break

                                    @default
                                        <input type="text"
                                               name="{{ $setting->key }}"
                                               value="{{ $setting->value }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @endswitch

                                <!-- Delete button for custom settings -->
                                @if(!in_array($setting->key, ['rt_name', 'rt_address', 'rt_description']))
                                    <div class="mt-2">
                                        <button type="button"
                                                onclick="deleteSetting('{{ $setting->key }}')"
                                                class="text-red-600 hover:text-red-800 text-sm">
                                            <i class="fas fa-trash mr-1"></i>Hapus
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="text-center">
            <button type="submit"
                    class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-save mr-2"></i>Simpan Perubahan
            </button>
        </div>
    </form>

    <!-- Delete Setting Form (Hidden) -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
<script>
    function deleteSetting(key) {
        if (confirm('Yakin ingin menghapus pengaturan ini?')) {
            const form = document.getElementById('delete-form');
            form.action = `/settings/${key}`;
            form.submit();
        }
    }
</script>
@endpush
