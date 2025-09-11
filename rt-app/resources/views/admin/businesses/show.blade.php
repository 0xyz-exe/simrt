@extends('layouts.app')
@section('title', 'Detail UMKM')
@section('content')
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('admin.businesses.index') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Kelola UMKM
        </a>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ $business->name }}</h1>
            <div class="flex items-center gap-2 mt-2">
                @if($business->is_active)
                    <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Aktif</span>
                @else
                    <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs rounded">Nonaktif</span>
                @endif

                @if($business->featured)
                    <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">⭐ Unggulan</span>
                @endif

                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                    {{ \App\Models\Business::getCategories()[$business->category] ?? $business->category }}
                </span>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.businesses.edit', $business) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Edit
            </a>

            <!-- Quick Actions -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                    Aksi ▼
                </button>
                <div x-show="open" @click.away="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                    <div class="py-1">
                        <!-- Toggle Status -->
                        <form method="POST" action="{{ route('admin.businesses.toggle-status', $business) }}" class="block">
                            @csrf
                            <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ $business->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>

                        <!-- Toggle Featured -->
                        <form method="POST" action="{{ route('admin.businesses.toggle-featured', $business) }}" class="block">
                            @csrf
                            <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ $business->featured ? 'Hapus dari Unggulan' : 'Jadikan Unggulan' }}
                            </button>
                        </form>

                        <hr class="my-1">

                        <!-- Delete -->
                        <form method="POST" action="{{ route('admin.businesses.destroy', $business) }}"
                              onsubmit="return confirm('Yakin hapus UMKM ini? Data tidak dapat dikembalikan.')">
                            @csrf
                            @method('DELETE')
                            <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                Hapus UMKM
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Business Image -->
            @if($business->image)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <img src="{{ asset('storage/' . $business->image) }}"
                         alt="{{ $business->name }}"
                         class="w-full h-64 object-cover">
                </div>
            @endif

            <!-- Description -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Deskripsi Usaha</h3>
                <div class="prose max-w-none">
                    {!! nl2br(e($business->description)) !!}
                </div>
            </div>

            <!-- Contact Information -->
            @if($business->address || $business->phone || $business->whatsapp || $business->email || $business->website)
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Informasi Kontak</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        @if($business->address)
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <div>
                                    <div class="font-medium">Alamat</div>
                                    <div class="text-sm text-gray-600">{{ $business->address }}</div>
                                </div>
                            </div>
                        @endif

                        @if($business->phone)
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <div>
                                    <div class="font-medium">Telepon</div>
                                    <div class="text-sm text-gray-600">
                                        <a href="tel:{{ $business->phone }}" class="hover:text-blue-600">{{ $business->phone }}</a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($business->whatsapp)
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"></path>
                                </svg>
                                <div>
                                    <div class="font-medium">WhatsApp</div>
                                    <div class="text-sm text-gray-600">
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}"
                                           target="_blank" class="hover:text-green-600">{{ $business->whatsapp }}</a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($business->email)
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <div class="font-medium">Email</div>
                                    <div class="text-sm text-gray-600">
                                        <a href="mailto:{{ $business->email }}" class="hover:text-blue-600">{{ $business->email }}</a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($business->website)
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                </svg>
                                <div>
                                    <div class="font-medium">Website</div>
                                    <div class="text-sm text-gray-600">
                                        <a href="{{ $business->website }}" target="_blank" class="hover:text-blue-600">{{ $business->website }}</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Social Media -->
            @if($business->instagram || $business->facebook)
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Media Sosial</h3>
                    <div class="flex gap-4">
                        @if($business->instagram)
                            <a href="https://instagram.com/{{ ltrim($business->instagram, '@') }}"
                               target="_blank"
                               class="flex items-center gap-2 px-3 py-2 bg-pink-100 text-pink-800 rounded hover:bg-pink-200">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                                {{ $business->instagram }}
                            </a>
                        @endif

                        @if($business->facebook)
                            <a href="https://facebook.com/{{ $business->facebook }}"
                               target="_blank"
                               class="flex items-center gap-2 px-3 py-2 bg-blue-100 text-blue-800 rounded hover:bg-blue-200">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                {{ $business->facebook }}
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Business Info -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Informasi Usaha</h3>
                <div class="space-y-3">
                    <div>
                        <div class="text-sm text-gray-500">Pemilik</div>
                        <div class="font-medium">{{ $business->owner_name }}</div>
                        @if($business->resident)
                            <div class="text-sm text-blue-600">
                                <a href="{{ route('admin.residents.show', $business->resident) }}" class="hover:underline">
                                    Lihat data resident →
                                </a>
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Kategori</div>
                        <div class="font-medium">
                            {{ \App\Models\Business::getCategories()[$business->category] ?? $business->category }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Status</div>
                        <div class="flex items-center gap-2">
                            @if($business->is_active)
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                <span class="text-green-600">Aktif</span>
                            @else
                                <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                <span class="text-red-600">Nonaktif</span>
                            @endif
                        </div>
                    </div>

                    @if($business->featured)
                        <div>
                            <div class="text-sm text-gray-500">Unggulan</div>
                            <div class="text-yellow-600">⭐ Ya</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Date Info -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Tanggal</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <div class="text-gray-500">Dibuat</div>
                        <div>{{ $business->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Terakhir diubah</div>
                        <div>{{ $business->updated_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.businesses.edit', $business) }}"
                       class="block w-full px-3 py-2 text-center bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                        Edit UMKM
                    </a>

                    @if($business->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}"
                           target="_blank"
                           class="block w-full px-3 py-2 text-center bg-green-100 text-green-700 rounded hover:bg-green-200">
                            Chat WhatsApp
                        </a>
                    @endif

                    @if($business->phone)
                        <a href="tel:{{ $business->phone }}"
                           class="block w-full px-3 py-2 text-center bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                            Telepon
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
