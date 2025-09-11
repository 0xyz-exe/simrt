@extends('layouts.app')
@section('title', 'Data Warga')
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
            <h1 class="text-2xl font-bold">Data Warga</h1>
            <p class="text-slate-600">Kelola data individual warga RT</p>
        </div>
        <a href="{{ route('residents.create') }}"
           class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            + Tambah Warga
        </a>
    </div>

    @if($residents->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left py-3 px-4">Nama</th>
                        <th class="text-left py-3 px-4">NIK</th>
                        <th class="text-left py-3 px-4">Keluarga</th>
                        <th class="text-left py-3 px-4">Kontak</th>
                        <th class="text-left py-3 px-4">Status</th>
                        <th class="text-left py-3 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($residents as $resident)
                        <tr class="border-t">
                            <td class="py-3 px-4">
                                <div class="font-semibold">{{ $resident->nama }}</div>
                                @if($resident->is_head)
                                    <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Kepala Keluarga</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-sm">{{ $resident->nik }}</td>
                            <td class="py-3 px-4">
                                <a href="{{ route('households.show', $resident->household) }}"
                                   class="text-green-600 hover:underline">
                                    {{ $resident->household->nomor_kk }}
                                </a>
                                <div class="text-sm text-slate-500">{{ $resident->household->alamat }}</div>
                            </td>
                            <td class="py-3 px-4 text-sm">
                                @if($resident->phone)
                                    <div>📞 {{ $resident->phone }}</div>
                                @endif
                                @if($resident->email)
                                    <div>📧 {{ $resident->email }}</div>
                                @endif
                                @if(!$resident->phone && !$resident->email)
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm">
                                    <div>RT {{ $resident->household->rt }}/RW {{ $resident->household->rw }}</div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('residents.show', $resident) }}"
                                       class="text-green-600 hover:underline text-sm">Detail</a>
                                    <a href="{{ route('residents.edit', $resident) }}"
                                       class="text-green-600 hover:underline text-sm">Edit</a>
                                    <form method="POST" action="{{ route('residents.destroy', $resident) }}"
                                          class="inline" onsubmit="return confirm('Yakin hapus data warga ini?')">
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
            <p class="text-slate-500 mb-4">Belum ada data warga</p>
            <a href="{{ route('residents.create') }}"
               class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Tambah Warga Pertama
            </a>
        </div>
    @endif
@endsection
