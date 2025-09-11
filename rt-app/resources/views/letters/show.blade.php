@extends('layouts.app')
@section('title', 'Detail Pengajuan Surat')
@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ auth()->user()->is_admin ? route('letters.index') : route('letters.mine') }}"
               class="text-green-600 hover:underline mb-2 inline-block">
                ← Kembali
            </a>
            <h1 class="text-2xl font-bold">Detail Pengajuan Surat</h1>
            <p class="text-slate-600">Informasi lengkap pengajuan surat</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <!-- Header Status -->
            <div class="flex justify-between items-start mb-6 pb-6 border-b">
                <div>
                    <h2 class="text-xl font-semibold">{{ ucfirst($letter->type) }}</h2>
                    <p class="text-slate-600 mt-1">{{ $letter->purpose }}</p>
                </div>
                <div class="text-right">
                    @if($letter->status === 'pending')
                        <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            🕐 Menunggu Persetujuan
                        </span>
                    @elseif($letter->status === 'approved')
                        <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            ✅ Disetujui
                        </span>
                    @else
                        <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                            ❌ Ditolak
                        </span>
                    @endif
                </div>
            </div>

            <!-- Detail Pemohon -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-3">Data Pemohon</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                        <p class="text-slate-900">{{ $letter->resident->nama }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">NIK</label>
                        <p class="text-slate-900">{{ $letter->resident->nik }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Tanggal Lahir</label>
                        <p class="text-slate-900">{{ $letter->resident->tanggal_lahir->format('d F Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Jenis Kelamin</label>
                        <p class="text-slate-900">{{ $letter->resident->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">Alamat</label>
                        <p class="text-slate-900">{{ $letter->resident->household->alamat }}</p>
                    </div>
                </div>
            </div>

            <!-- Detail Pengajuan -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-3">Detail Pengajuan</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Jenis Surat</label>
                        <p class="text-slate-900">{{ ucfirst($letter->type) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Tanggal Pengajuan</label>
                        <p class="text-slate-900">{{ $letter->created_at->format('d F Y, H:i') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">Keperluan</label>
                        <p class="text-slate-900">{{ $letter->purpose }}</p>
                    </div>
                    @if($letter->notes)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Catatan Tambahan</label>
                            <p class="text-slate-900">{{ $letter->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-3">Timeline Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <div>
                            <p class="text-slate-900 font-medium">Pengajuan dibuat</p>
                            <p class="text-sm text-slate-500">{{ $letter->created_at->format('d F Y, H:i') }}</p>
                        </div>
                    </div>
                    @if($letter->approved_at)
                        <div class="flex items-center">
                            <div class="w-3 h-3 {{ $letter->status === 'approved' ? 'bg-green-500' : 'bg-red-500' }} rounded-full mr-3"></div>
                            <div>
                                <p class="text-slate-900 font-medium">
                                    {{ $letter->status === 'approved' ? 'Pengajuan disetujui' : 'Pengajuan ditolak' }}
                                </p>
                                <p class="text-sm text-slate-500">{{ $letter->approved_at->format('d F Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-6 border-t">
                @can('admin')
                    @if($letter->status === 'pending')
                        <form method="POST" action="{{ route('letters.approve', $letter) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                ✅ Setujui
                            </button>
                        </form>
                        <form method="POST" action="{{ route('letters.reject', $letter) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                                    onclick="return confirm('Yakin ingin menolak pengajuan ini?')">
                                ❌ Tolak
                            </button>
                        </form>
                    @endif
                @endcan

                @if($letter->status === 'approved')
                    <a href="{{ route('letters.download', $letter) }}"
                       class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        📄 Download Surat
                    </a>
                @endif

                <a href="{{ auth()->user()->is_admin ? route('letters.index') : route('letters.mine') }}"
                   class="px-6 py-2 border border-slate-300 text-slate-700 rounded-md hover:bg-slate-50">
                    Kembali
                </a>
            </div>
        </div>
    </div>
@endsection
