@extends('layouts.app')
@section('title', 'Data Keluarga')
@section('content')
    <!-- Back Button -->
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
            <h1 class="text-2xl font-bold">Data Keluarga</h1>
            <p class="text-slate-600">Kelola data keluarga yang terdaftar di RT</p>
        </div>
        <a href="{{ route('households.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + Tambah Keluarga
        </a>
    </div>

    @if($households->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left py-3 px-4">No. KK</th>
                        <th class="text-left py-3 px-4">Alamat</th>
                        <th class="text-left py-3 px-4">RT/RW</th>
                        <th class="text-left py-3 px-4">Kepala Keluarga</th>
                        <th class="text-left py-3 px-4">Jumlah Anggota</th>
                        <th class="text-left py-3 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($households as $household)
                        <tr class="border-t">
                            <td class="py-3 px-4 font-mono">{{ $household->nomor_kk }}</td>
                            <td class="py-3 px-4">{{ $household->alamat }}</td>
                            <td class="py-3 px-4">{{ $household->rt }}/{{ $household->rw }}</td>
                            <td class="py-3 px-4">
                                @if($household->kepalaKeluarga)
                                    <div class="font-semibold">{{ $household->kepalaKeluarga->nama }}</div>
                                    <div class="text-sm text-slate-500">{{ $household->kepalaKeluarga->nik }}</div>
                                @else
                                    <span class="text-slate-400">Belum ditentukan</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-sm rounded">
                                    {{ $household->residents->count() }} orang
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('households.show', $household) }}"
                                       class="text-blue-600 hover:underline text-sm">Detail</a>
                                    <a href="{{ route('households.edit', $household) }}"
                                       class="text-green-600 hover:underline text-sm">Edit</a>
                                    <form method="POST" action="{{ route('households.destroy', $household) }}"
                                          class="inline" onsubmit="return confirm('Yakin hapus data keluarga ini?')">
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
            <p class="text-slate-500 mb-4">Belum ada data keluarga</p>
            <a href="{{ route('households.create') }}"
               class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Tambah Keluarga Pertama
            </a>
        </div>
    @endif
@endsection
