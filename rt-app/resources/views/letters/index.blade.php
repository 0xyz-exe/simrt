@extends('layouts.app')
@section('title', 'Kelola Pengajuan Surat')
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
            <h1 class="text-2xl font-bold">Pengajuan Surat</h1>
            <p class="text-slate-600">Kelola persetujuan surat dari warga</p>
        </div>
    </div>

    <!-- Filter Status -->
    <div class="mb-6 flex gap-2" x-data="{ filter: '{{ request('status') ?? 'all' }}' }">
        <button @click="window.location.href = '{{ route('letters.index') }}'"
                :class="filter === 'all' ? 'bg-green-600 text-white' : 'bg-white text-slate-700'"
                class="px-4 py-2 rounded border border-slate-300 hover:bg-slate-50">
            Semua ({{ $letters->total() }})
        </button>
        <button @click="window.location.href = '{{ route('letters.index', ['status' => 'pending']) }}'"
                :class="filter === 'pending' ? 'bg-yellow-600 text-white' : 'bg-white text-slate-700'"
                class="px-4 py-2 rounded border border-slate-300 hover:bg-slate-50">
            Pending ({{ \App\Models\Letter::where('status', 'pending')->count() }})
        </button>
        <button @click="window.location.href = '{{ route('letters.index', ['status' => 'approved']) }}'"
                :class="filter === 'approved' ? 'bg-green-600 text-white' : 'bg-white text-slate-700'"
                class="px-4 py-2 rounded border border-slate-300 hover:bg-slate-50">
            Disetujui ({{ \App\Models\Letter::where('status', 'approved')->count() }})
        </button>
        <button @click="window.location.href = '{{ route('letters.index', ['status' => 'rejected']) }}'"
                :class="filter === 'rejected' ? 'bg-red-600 text-white' : 'bg-white text-slate-700'"
                class="px-4 py-2 rounded border border-slate-300 hover:bg-slate-50">
            Ditolak ({{ \App\Models\Letter::where('status', 'rejected')->count() }})
        </button>
    </div>

    @if($letters->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Pemohon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jenis Surat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Keperluan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($letters as $letter)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-slate-900">{{ $letter->resident->nama }}</div>
                                    <div class="text-sm text-slate-500">{{ $letter->resident->household->alamat }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ ucfirst($letter->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-900">{{ $letter->purpose }}</div>
                                @if($letter->notes)
                                    <div class="text-xs text-slate-500 mt-1">{{ $letter->notes }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($letter->status === 'pending')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Menunggu
                                    </span>
                                @elseif($letter->status === 'approved')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $letter->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    @if($letter->status === 'pending')
                                        <form method="POST" action="{{ route('letters.approve', $letter) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                Setujui
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('letters.reject', $letter) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700"
                                                    onclick="return confirm('Yakin ingin menolak pengajuan ini?')">
                                                Tolak
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('letters.show', $letter) }}" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $letters->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="text-slate-400 text-6xl mb-4">📝</div>
            <h3 class="text-lg font-semibold text-slate-600 mb-2">Belum ada pengajuan surat</h3>
            <p class="text-slate-500">Pengajuan surat dari warga akan muncul di sini</p>
        </div>
    @endif
@endsection
