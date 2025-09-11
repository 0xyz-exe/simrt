@extends('layouts.landing')

@section('title', 'UMKM Warga - Guyub Rukun Warga RT 02/RW 04 Kedurus - Kepatihan Kedurus Kepatihan')
@section('description', 'Daftar Usaha Mikro Kecil Menengah (UMKM) dari warga RT 02/RW 04 Kedurus - Kepatihan Kedurus Kepatihan yang dapat Anda dukung')

@section('content')
<!-- Header Section -->
<section class="hero-gradient py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                <i class="fas fa-store mr-3"></i>UMKM Warga
            </h1>
            <p class="text-xl opacity-90 max-w-2xl mx-auto">
                Dukung usaha mikro dari warga RT 02/RW 04 Kedurus - Kepatihan  untuk memajukan ekonomi lokal
            </p>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<nav class="bg-white border-b border-gray-200 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="{{ route('landing') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-900 font-medium">UMKM</li>
        </ol>
    </div>
</nav>

<!-- Filter Section -->
<section class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('landing.businesses') }}" class="bg-white rounded-lg shadow-md p-6">
            <div class="grid md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari UMKM</label>
                    <div class="relative">
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Nama usaha, pemilik, atau deskripsi..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select id="category"
                            name="category"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full rt-gradient text-white px-6 py-2 rounded-lg hover:opacity-90 transition-all">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Results Section -->
<section class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Results Info -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    @if(request('search') || request('category'))
                        Hasil Pencarian
                    @else
                        Semua UMKM
                    @endif
                </h2>
                <p class="text-gray-600">
                    Ditemukan {{ $businesses->total() }} UMKM
                    @if(request('search'))
                        untuk "{{ request('search') }}"
                    @endif
                    @if(request('category'))
                        kategori {{ $categories[request('category')] ?? request('category') }}
                    @endif
                </p>
            </div>

            @if(request('search') || request('category'))
                <a href="{{ route('landing.businesses') }}"
                   class="text-rt-green hover:text-rt-green font-medium">
                    <i class="fas fa-times mr-1"></i>Reset Filter
                </a>
            @endif
        </div>

        @if($businesses->count() > 0)
            <!-- UMKM Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($businesses as $business)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover animate-fade-in">
                    <!-- Image -->
                    <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                        <img src="{{ $business->image_url }}"
                             alt="{{ $business->name }}"
                             class="w-full h-48 object-cover">
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <!-- Category & Featured Badge -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                {{ $categories[$business->category] ?? $business->category }}
                            </span>
                            @if($business->featured)
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full">
                                    <i class="fas fa-star mr-1"></i>Unggulan
                                </span>
                            @endif
                        </div>

                        <!-- Business Info -->
                        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $business->name }}</h3>
                        <p class="text-gray-600 text-sm mb-3 flex items-center">
                            <i class="fas fa-user mr-2"></i>{{ $business->owner_name }}
                        </p>
                        <p class="text-gray-600 mb-4 line-clamp-3">{{ Str::limit($business->description, 120) }}</p>

                        <!-- Address -->
                        <div class="flex items-start text-sm text-gray-500 mb-4">
                            <i class="fas fa-map-marker-alt mr-2 mt-1"></i>
                            <span class="line-clamp-2">{{ $business->address }}</span>
                        </div>

                        <!-- Contact & Action -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex space-x-3">
                                @if($business->whatsapp)
                                    <a href="{{ $business->whatsapp_link }}"
                                       target="_blank"
                                       class="text-green-600 hover:text-green-700 transition-colors"
                                       title="Chat WhatsApp">
                                        <i class="fab fa-whatsapp text-lg"></i>
                                    </a>
                                @endif

                                @if($business->instagram)
                                    <a href="https://instagram.com/{{ ltrim($business->instagram, '@') }}"
                                       target="_blank"
                                       class="text-pink-600 hover:text-pink-700 transition-colors"
                                       title="Instagram">
                                        <i class="fab fa-instagram text-lg"></i>
                                    </a>
                                @endif

                                @if($business->facebook)
                                    <a href="https://facebook.com/{{ $business->facebook }}"
                                       target="_blank"
                                       class="text-green-600 hover:text-green-700 transition-colors"
                                       title="Facebook">
                                        <i class="fab fa-facebook text-lg"></i>
                                    </a>
                                @endif

                                @if($business->website)
                                    <a href="{{ $business->website }}"
                                       target="_blank"
                                       class="text-gray-600 hover:text-gray-700 transition-colors"
                                       title="Website">
                                        <i class="fas fa-globe text-lg"></i>
                                    </a>
                                @endif
                            </div>

                            <a href="{{ route('landing.business-detail', $business) }}"
                               class="text-rt-green hover:text-rt-green font-semibold text-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($businesses->hasPages())
            <div class="mt-12">
                <nav class="flex justify-center">
                    <div class="flex items-center space-x-2">
                        {{-- Previous Page Link --}}
                        @if ($businesses->onFirstPage())
                            <span class="px-4 py-2 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                <i class="fas fa-chevron-left mr-1"></i>Sebelumnya
                            </span>
                        @else
                            <a href="{{ $businesses->previousPageUrl() }}" class="px-4 py-2 text-rt-green bg-white border border-gray-300 rounded-lg hover:bg-green-50 transition-colors">
                                <i class="fas fa-chevron-left mr-1"></i>Sebelumnya
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($businesses->getUrlRange(1, $businesses->lastPage()) as $page => $url)
                            @if ($page == $businesses->currentPage())
                                <span class="px-4 py-2 text-white rt-gradient rounded-lg font-semibold">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($businesses->hasMorePages())
                            <a href="{{ $businesses->nextPageUrl() }}" class="px-4 py-2 text-rt-green bg-white border border-gray-300 rounded-lg hover:bg-green-50 transition-colors">
                                Selanjutnya<i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        @else
                            <span class="px-4 py-2 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                Selanjutnya<i class="fas fa-chevron-right ml-1"></i>
                            </span>
                        @endif
                    </div>
                </nav>

                <div class="text-center mt-4 text-sm text-gray-600">
                    Menampilkan {{ $businesses->firstItem() }}-{{ $businesses->lastItem() }} dari {{ $businesses->total() }} UMKM
                </div>
            </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-store text-gray-400 text-5xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">
                    @if(request('search') || request('category'))
                        Tidak Ada UMKM Ditemukan
                    @else
                        Belum Ada UMKM Terdaftar
                    @endif
                </h2>
                <p class="text-xl text-gray-600 mb-8 max-w-lg mx-auto">
                    @if(request('search') || request('category'))
                        Coba ubah kata kunci pencarian atau filter kategori yang Anda gunakan.
                    @else
                        UMKM dari warga RT akan ditampilkan di halaman ini ketika sudah terdaftar.
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @if(request('search') || request('category'))
                        <a href="{{ route('landing.businesses') }}"
                           class="inline-flex items-center rt-gradient text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition-colors">
                            <i class="fas fa-search mr-2"></i>Lihat Semua UMKM
                        </a>
                    @endif
                    <a href="{{ route('landing') }}"
                       class="inline-flex items-center border border-rt-green text-rt-green px-6 py-3 rounded-lg font-semibold hover:bg-green-50 transition-colors">
                        <i class="fas fa-home mr-2"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Support Local Business CTA -->
<section class="py-16 hero-gradient">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Dukung UMKM Warga RT
        </h2>
        <p class="text-xl opacity-90 mb-8">
            Dengan mendukung usaha mikro dari warga RT, kita turut memajukan ekonomi lokal dan memperkuat komunitas
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('login') }}"
               class="bg-white text-rt-green px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                <i class="fas fa-user-plus mr-2"></i>Daftarkan UMKM Anda
            </a>
            <a href="{{ route('landing') }}"
               class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-rt-green transition-colors">
                <i class="fas fa-info-circle mr-2"></i>Pelajari Lebih Lanjut
            </a>
        </div>
    </div>
</section>
@endsection

