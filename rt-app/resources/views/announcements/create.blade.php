@extends('layouts.app')
@section('title', 'Buat Pengumuman')
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Buat Pengumuman</h1>
        <p class="text-slate-600">Buat pengumuman baru untuk warga RT</p>
    </div>

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
        <form method="POST" action="{{ route('announcements.store') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-2">Judul Pengumuman</label>
                <input name="title" type="text" required
                       value="{{ old('title') }}"
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
                          placeholder="Tulis isi pengumuman di sini...">{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Tanggal Publikasi (Opsional)</label>
                <input name="published_at" type="datetime-local"
                       value="{{ old('published_at') }}"
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
                    Simpan Draft
                </button>
                <a href="{{ route('announcements.index') }}"
                   class="px-6 py-2 border rounded hover:bg-slate-50">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
