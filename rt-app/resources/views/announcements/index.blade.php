@extends('layouts.app')
@section('title', 'Kelola Pengumuman')
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
            <h1 class="text-2xl font-bold">Kelola Pengumuman</h1>
            <p class="text-slate-600">Buat dan kelola pengumuman untuk warga RT</p>
        </div>
        <a href="{{ route('announcements.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + Buat Pengumuman
        </a>
    </div>

    @if($announcements->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left py-3 px-4">Judul</th>
                        <th class="text-left py-3 px-4">Status</th>
                        <th class="text-left py-3 px-4">Dibuat</th>
                        <th class="text-left py-3 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($announcements as $announcement)
                        <tr class="border-t">
                            <td class="py-3 px-4">
                                <div class="font-semibold">{{ $announcement->title }}</div>
                                <div class="text-sm text-slate-500">
                                    {{ Str::limit($announcement->content, 100) }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @if($announcement->published_at)
                                    <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                                        Dipublikasi
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-sm">
                                {{ $announcement->created_at->format('d M Y, H:i') }}
                                @if($announcement->creator)
                                    <br><span class="text-slate-500">oleh {{ $announcement->creator->name }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('announcements.edit', $announcement) }}"
                                       class="text-blue-600 hover:underline text-sm">Edit</a>
                                    <form method="POST" action="{{ route('announcements.destroy', $announcement) }}"
                                          class="inline" onsubmit="return confirm('Yakin hapus pengumuman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline text-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <p class="text-slate-500 mb-4">Belum ada pengumuman</p>
            <a href="{{ route('announcements.create') }}"
               class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Buat Pengumuman Pertama
            </a>
        </div>
    @endif
@endsection
