@extends('layouts.app')
@section('title', 'Edit Pengumuman')
@section('content')
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('announcements.index') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Pengumuman
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold">Edit Pengumuman</h1>
        <p class="text-slate-600">Edit pengumuman untuk warga RT</p>
    </div>

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
        <form method="POST" action="{{ route('announcements.update', $announcement) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold mb-2">Judul Pengumuman</label>
                <input name="title" type="text" required
                       value="{{ old('title', $announcement->title) }}"
                       class="w-full border rounded px-3 py-2 focus:border-blue-500 focus:outline-none"
                       placeholder="Masukkan judul pengumuman">
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Isi Pengumuman</label>
                <textarea name="content" required rows="8"
                          class="w-full border rounded px-3 py-2 focus:border-blue-500 focus:outline-none"
                          placeholder="Tulis isi pengumuman di sini...">{{ old('content', $announcement->content) }}</textarea>
                @error('content')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Tanggal Publikasi (Opsional)</label>
                <input name="published_at" type="datetime-local"
                       value="{{ old('published_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '') }}"
                       class="w-full border rounded px-3 py-2 focus:border-blue-500 focus:outline-none">
                <p class="text-sm text-slate-500 mt-1">Kosongkan untuk simpan sebagai draft</p>
                @error('published_at')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" name="publish_now" value="1"
                        class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Publikasi Sekarang
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-slate-600 text-white rounded hover:bg-slate-700">
                    Simpan Perubahan
                </button>
                <a href="{{ route('announcements.index') }}"
                   class="px-6 py-2 border rounded hover:bg-slate-50">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
