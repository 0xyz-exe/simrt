@extends('layouts.app')
@section('title', 'Detail Warga')
@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Detail Warga</h1>
                <p class="text-slate-600">Informasi lengkap data warga</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('residents.edit', $resident) }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Edit Data
                </a>
                <a href="{{ route('residents.index') }}"
                   class="px-4 py-2 border border-slate-300 text-slate-700 rounded hover:bg-slate-50">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold">Informasi Pribadi</h2>
                    @if($resident->is_head)
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                            Kepala Keluarga
                        </span>
                    @endif
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">NIK</label>
                        <p class="text-lg font-mono">{{ $resident->nik }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
                        <p class="text-lg font-semibold">{{ $resident->nama }}</p>
                    </div>

                    @if($resident->tempat_lahir)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tempat Lahir</label>
                        <p class="text-lg">{{ $resident->tempat_lahir }}</p>
                    </div>
                    @endif

                    @if($resident->tanggal_lahir)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Lahir</label>
                        <p class="text-lg">{{ $resident->tanggal_lahir->format('d F Y') }}</p>
                        <p class="text-sm text-gray-500">{{ $resident->tanggal_lahir->age }} tahun</p>
                    </div>
                    @endif

                    @if($resident->jenis_kelamin)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Kelamin</label>
                        <p class="text-lg">{{ $resident->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    @endif

                    @if($resident->phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">No. Telepon</label>
                        <p class="text-lg">
                            <a href="tel:{{ $resident->phone }}" class="text-blue-600 hover:underline">
                                📞 {{ $resident->phone }}
                            </a>
                        </p>
                    </div>
                    @endif

                    @if($resident->email)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-lg">
                            <a href="mailto:{{ $resident->email }}" class="text-blue-600 hover:underline">
                                📧 {{ $resident->email }}
                            </a>
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Household Information -->
        <div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Data Keluarga</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">No. KK</label>
                        <p class="text-lg font-mono">{{ $resident->household->nomor_kk }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Alamat</label>
                        <p class="text-lg">{{ $resident->household->alamat }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">RT</label>
                            <p class="text-lg">{{ $resident->household->rt }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">RW</label>
                            <p class="text-lg">{{ $resident->household->rw }}</p>
                        </div>
                    </div>

                    @if($resident->household->kepalaKeluarga && $resident->household->kepalaKeluarga->id !== $resident->id)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Kepala Keluarga</label>
                        <p class="text-lg">{{ $resident->household->kepalaKeluarga->nama }}</p>
                    </div>
                    @endif

                    <div class="pt-4 border-t">
                        <a href="{{ route('households.show', $resident->household) }}"
                           class="text-blue-600 hover:underline text-sm">
                            → Lihat Detail Keluarga
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Aksi Cepat</h3>

                <div class="space-y-3">
                    <a href="{{ route('letters.create') }}?resident_id={{ $resident->id }}"
                       class="block w-full px-4 py-2 bg-green-600 text-white text-center rounded hover:bg-green-700">
                        📝 Buat Surat
                    </a>

                    @if($resident->letters->count() > 0)
                    <a href="{{ route('letters.mine') }}?resident_id={{ $resident->id }}"
                       class="block w-full px-4 py-2 border border-blue-600 text-blue-600 text-center rounded hover:bg-blue-50">
                        📋 Lihat Riwayat Surat ({{ $resident->letters->count() }})
                    </a>
                    @endif

                    <form method="POST" action="{{ route('residents.destroy', $resident) }}"
                          class="inline w-full"
                          onsubmit="return confirm('Yakin hapus data warga ini? Data yang dihapus tidak dapat dikembalikan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            🗑️ Hapus Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($resident->letters->count() > 0)
    <!-- Recent Letters -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Riwayat Surat Terbaru</h2>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Tanggal</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Jenis Surat</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Status</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resident->letters->take(5) as $letter)
                    <tr class="border-t">
                        <td class="py-3 px-4 text-sm">{{ $letter->created_at->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 text-sm capitalize">{{ $letter->type }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs rounded
                                {{ $letter->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                   ($letter->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($letter->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('letters.show', $letter) }}"
                               class="text-blue-600 hover:underline text-sm">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($resident->letters->count() > 5)
        <div class="mt-4 text-center">
            <a href="{{ route('letters.mine') }}?resident_id={{ $resident->id }}"
               class="text-blue-600 hover:underline text-sm">
                Lihat Semua Surat ({{ $resident->letters->count() }})
            </a>
        </div>
        @endif
    </div>
    @endif
@endsection
