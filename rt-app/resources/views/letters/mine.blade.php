@extends('layouts.app')
@section('title', 'Riwayat Surat Saya')
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
            <h1 class="text-2xl font-bold">Riwayat Surat Saya</h1>
            <p class="text-slate-600">Lihat status dan download surat yang telah diajukan</p>
        </div>
        <a href="{{ route('letters.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            + Ajukan Surat Baru
        </a>
    </div>

    @if($letters->count() > 0)
        <div class="space-y-4">
            @foreach($letters as $letter)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">{{ ucfirst($letter->type) }}</h3>
                            <p class="text-slate-600">{{ $letter->purpose }}</p>
                            @if($letter->notes)
                                <p class="text-sm text-slate-500 mt-1">Catatan: {{ $letter->notes }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            @if($letter->status === 'pending')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Menunggu Persetujuan
                                </span>
                            @elseif($letter->status === 'approved')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    Disetujui
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                    Ditolak
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="grid md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-slate-500">Tanggal Pengajuan:</span>
                                <span class="font-medium">{{ $letter->created_at->format('d F Y, H:i') }}</span>
                            </div>
                            @if($letter->approved_at)
                                <div>
                                    <span class="text-slate-500">Tanggal {{ $letter->status === 'approved' ? 'Disetujui' : 'Ditolak' }}:</span>
                                    <span class="font-medium">{{ $letter->approved_at->format('d F Y, H:i') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 flex gap-2">
                            @if($letter->status === 'approved')
                                <a href="{{ route('letters.download', $letter) }}"
                                   class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                    📄 Download Surat
                                </a>
                            @endif
                            <a href="{{ route('letters.show', $letter) }}" class="px-4 py-2 bg-slate-600 text-white text-sm rounded hover:bg-slate-700">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $letters->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="text-slate-400 text-6xl mb-4">📝</div>
            <h3 class="text-lg font-semibold text-slate-600 mb-2">Belum ada pengajuan surat</h3>
            <p class="text-slate-500 mb-4">Anda belum pernah mengajukan surat</p>
            <a href="{{ route('letters.create') }}" class="inline-block px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Ajukan Surat Pertama
            </a>
        </div>
    @endif

    <!-- Status Info -->
    <div class="mt-6 p-4 bg-slate-50 rounded-lg">
        <h4 class="font-medium text-slate-700 mb-2">ℹ️ Keterangan Status</h4>
        <div class="grid md:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 mr-2">
                    Menunggu
                </span>
                <span class="text-slate-600">Sedang diproses admin</span>
            </div>
            <div>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mr-2">
                    Disetujui
                </span>
                <span class="text-slate-600">Surat dapat didownload</span>
            </div>
            <div>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 mr-2">
                    Ditolak
                </span>
                <span class="text-slate-600">Pengajuan tidak disetujui</span>
            </div>
        </div>
    </div>
@endsection
