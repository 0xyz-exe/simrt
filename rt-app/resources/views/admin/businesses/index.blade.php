@extends('layouts.app')
@section('title', 'Kelola UMKM')
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
            <h1 class="text-2xl font-bold">Kelola UMKM</h1>
            <p class="text-slate-600">Manage usaha mikro, kecil dan menengah warga RT</p>
        </div>
        <a href="{{ route('admin.businesses.create') }}"
           class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            + Tambah UMKM
        </a>
    </div>

    @if($businesses->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left py-3 px-4">Nama Usaha</th>
                        <th class="text-left py-3 px-4">Pemilik</th>
                        <th class="text-left py-3 px-4">Kategori</th>
                        <th class="text-left py-3 px-4">Status</th>
                        <th class="text-left py-3 px-4">Unggulan</th>
                        <th class="text-left py-3 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($businesses as $business)
                        <tr class="border-t">
                            <td class="py-3 px-4">
                                <div class="font-semibold">{{ $business->name }}</div>
                                <div class="text-sm text-slate-500">
                                    {{ Str::limit($business->description, 50) }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium">{{ $business->owner_name }}</div>
                                @if($business->resident)
                                    <div class="text-sm text-slate-500">{{ $business->resident->nama }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                                    {{ \App\Models\Business::getCategories()[$business->category] ?? $business->category }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($business->is_active)
                                    <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs rounded">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($business->featured)
                                    <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">
                                        ⭐ Unggulan
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2 flex-wrap">
                                    <a href="{{ route('admin.businesses.show', $business) }}"
                                       class="text-blue-600 hover:underline text-sm">Lihat</a>
                                    <a href="{{ route('admin.businesses.edit', $business) }}"
                                       class="text-green-600 hover:underline text-sm">Edit</a>

                                    <!-- Toggle Status -->
                                    <form method="POST" action="{{ route('admin.businesses.toggle-status', $business) }}" class="inline">
                                        @csrf
                                        <button class="text-yellow-600 hover:underline text-sm">
                                            {{ $business->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <!-- Toggle Featured -->
                                    <form method="POST" action="{{ route('admin.businesses.toggle-featured', $business) }}" class="inline">
                                        @csrf
                                        <button class="text-purple-600 hover:underline text-sm">
                                            {{ $business->featured ? 'Tidak Unggulan' : 'Jadikan Unggulan' }}
                                        </button>
                                    </form>

                                    <!-- Delete -->
                                    <form method="POST" action="{{ route('admin.businesses.destroy', $business) }}"
                                          class="inline" onsubmit="return confirm('Yakin hapus UMKM ini?')">
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

        <!-- Pagination -->
        @if($businesses->hasPages())
            <div class="mt-6">
                {{ $businesses->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <p class="text-slate-500 mb-4">Belum ada UMKM terdaftar</p>
            <a href="{{ route('admin.businesses.create') }}"
               class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Tambah UMKM Pertama
            </a>
        </div>
    @endif

    <!-- Statistics Summary -->
    <div class="mt-8 grid md:grid-cols-4 gap-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-2xl font-bold text-blue-600">{{ $businesses->count() }}</div>
            <div class="text-sm text-slate-600">Total UMKM</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-2xl font-bold text-green-600">{{ $businesses->where('is_active', true)->count() }}</div>
            <div class="text-sm text-slate-600">Aktif</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-2xl font-bold text-yellow-600">{{ $businesses->where('featured', true)->count() }}</div>
            <div class="text-sm text-slate-600">Unggulan</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-2xl font-bold text-purple-600">{{ $businesses->groupBy('category')->count() }}</div>
            <div class="text-sm text-slate-600">Kategori</div>
        </div>
    </div>
@endsection
