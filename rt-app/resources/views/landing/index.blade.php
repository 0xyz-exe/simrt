@extends('layouts.landing')

@section('title', 'Beranda - Sistem Informasi RT')
@section('description', 'Portal resmi Rukun Tetangga dengan layanan pengumuman, UMKM warga, dan transparansi iuran')

@section('extra_css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous"/>
<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    #rt-interactive-map {
        background: #eef2f1;
        z-index: 1;
        cursor: grab;
    }
    #rt-interactive-map:active {
        cursor: grabbing;
    }
    .leaflet-control-attribution {
        font-size: 10px;
        background: rgba(255, 255, 255, 0.8);
    }
    .leaflet-container {
        font: inherit;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 12px;
    }
    .leaflet-control-zoom {
        border: none !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
    .leaflet-control-zoom a {
        background: white !important;
        color: #374151 !important;
        border: none !important;
        border-radius: 8px !important;
        margin: 2px !important;
        font-size: 18px !important;
        font-weight: bold !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }
    .leaflet-control-zoom a:hover {
        background: #f3f4f6 !important;
        color: #16a34a !important;
    }
    .leaflet-control-layers {
        background: rgba(255, 255, 255, 0.95) !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        border: none !important;
        padding: 8px !important;
    }
    .stat-update {
        animation: statPulse 0.6s ease-in-out;
        color: #3b82f6 !important;
    }
    @keyframes statPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    .real-time-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        animation: pulse 2s infinite;
        margin-left: 8px;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero-gradient relative overflow-hidden">
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-24 text-center text-white">
        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-4 sm:mb-6">
            Selamat Datang di Portal Warga
        </h1>
        <p class="text-base sm:text-lg lg:text-xl text-green-100 mb-6 sm:mb-8">
            Informasi RT, dukungan UMKM warga, dan transparansi iuran dalam satu platform
        </p>
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
            <a href="{{ route('landing.announcements') }}" class="bg-white text-green-600 px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors text-sm sm:text-base">
                <i class="fas fa-bullhorn mr-2"></i>Lihat Pengumuman
            </a>
            <a href="{{ route('login') }}" class="border-2 border-white text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:bg-white hover:text-green-600 transition-colors text-sm sm:text-base">
                <i class="fas fa-sign-in-alt mr-2"></i>Masuk
            </a>
        </div>
    </div>
</section>

<!-- Quick Stats Section -->
<section class="py-10 sm:py-12 lg:py-14 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 animate-on-scroll">
            <div class="bg-gray-50 rounded-xl p-4 sm:p-5 lg:p-6 shadow-sm">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                        <i class="fas fa-home text-lg sm:text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500">KK Terdata</p>
                        <p id="stat-households" class="text-xl sm:text-2xl font-bold text-gray-800">{{ $profileData['total_households'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 sm:p-5 lg:p-6 shadow-sm">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-users text-lg sm:text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500">Jumlah Warga</p>
                        <p id="stat-residents" class="text-xl sm:text-2xl font-bold text-gray-800">{{ $profileData['total_residents'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 sm:p-5 lg:p-6 shadow-sm">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center">
                        <i class="fas fa-store text-lg sm:text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500">UMKM Aktif</p>
                        <p id="stat-businesses" class="text-xl sm:text-2xl font-bold text-gray-800">{{ $businessCategories ? $businessCategories->sum() : 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 sm:p-5 lg:p-6 shadow-sm">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                        <i class="fas fa-bullhorn text-lg sm:text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500">Pengumuman Baru</p>
                        <p id="stat-announcements" class="text-xl sm:text-2xl font-bold text-gray-800">{{ $announcements->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Activities Section -->
<section class="py-12 sm:py-16 lg:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-6 sm:mb-8 lg:mb-10 animate-on-scroll">
            <div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800">🗓️ Kegiatan Terbaru</h2>
                <p class="text-gray-600 mt-2">Jadwal kegiatan untuk warga RT</p>
            </div>
            <a href="{{ route('landing.activities') }}" class="hidden md:inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold">
                Lihat Semua<i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        @if($featuredActivities->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                @foreach($featuredActivities as $activity)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover animate-on-scroll">
                        <div class="md:flex h-full">
                            <div class="md:w-56 h-44 md:h-full flex-shrink-0">
                                @if($activity->image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($activity->image) }}" alt="{{ $activity->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                        <i class="fas fa-calendar-alt text-white text-4xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-5 flex flex-col justify-between flex-1">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2">{{ $activity->title }}</h3>
                                    <p class="text-xs text-gray-500 mb-2">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ \Carbon\Carbon::parse($activity->start_date)->format('d M Y H:i') }}
                                    </p>
                                    @if($activity->location)
                                        <p class="text-xs text-gray-500 mb-3">
                                            <i class="fas fa-map-marker-alt mr-1 text-red-500"></i>{{ $activity->location }}
                                        </p>
                                    @endif
                                    <p class="text-sm text-gray-600 line-clamp-3">{{ Str::limit(strip_tags($activity->description), 120) }}</p>
                                </div>
                                <div class="mt-4 flex items-center justify-between">
                                    <span class="inline-flex items-center text-xs font-medium bg-green-100 text-green-700 px-2 py-1 rounded-full">
                                        <i class="fas fa-tag mr-1"></i>Kegiatan
                                    </span>
                                    <a href="{{ route('landing.activity-detail', $activity) }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">Detail →</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-6 md:hidden">
                <a href="{{ route('landing.activities') }}" class="inline-flex items-center bg-green-600 text-white px-5 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">Lihat Semua<i class="fas fa-arrow-right ml-2"></i></a>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-calendar text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-600">Belum ada kegiatan</p>
            </div>
        @endif
    </div>
</section>

<!-- UMKM Section -->
<section class="py-12 sm:py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-6 sm:mb-8 lg:mb-10 animate-on-scroll">
            <div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800">🛍️ UMKM Unggulan</h2>
                <p class="text-gray-600 mt-2">Dukung usaha warga sekitar</p>
            </div>
            <a href="{{ route('landing.businesses') }}" class="hidden md:inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold">
                Lihat Semua<i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        @if($featuredBusinesses->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @foreach($featuredBusinesses as $business)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover animate-on-scroll flex flex-col">
                        <div class="h-44 w-full">
                            @if($business->image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($business->image) }}" alt="{{ $business->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                                    <i class="fas fa-store text-white text-4xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-5 flex flex-col flex-1">
                            <h3 class="text-lg font-bold text-gray-800 mb-1 line-clamp-1">{{ $business->name }}</h3>
                            <p class="text-xs text-gray-500 mb-2 flex items-center">
                                <i class="fas fa-tag mr-1 text-green-500"></i>
                                {{ App\Models\Business::getCategories()[$business->category] ?? $business->category }}
                            </p>
                            <p class="text-sm text-gray-600 line-clamp-3 mb-4">{{ Str::limit(strip_tags($business->description), 110) }}</p>
                            <div class="mt-auto flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    @if($business->whatsapp_link)
                                        <a href="{{ $business->whatsapp_link }}" target="_blank" class="inline-flex items-center text-green-600 hover:text-green-700 font-semibold text-xs">
                                            <i class="fab fa-whatsapp mr-1"></i>Chat
                                        </a>
                                    @endif
                                </div>
                                <a href="{{ route('landing.business-detail', $business) }}" class="text-blue-600 hover:text-blue-700 font-semibold text-xs">Detail →</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-6 md:hidden">
                <a href="{{ route('landing.businesses') }}" class="inline-flex items-center bg-green-600 text-white px-5 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">Lihat Semua<i class="fas fa-arrow-right ml-2"></i></a>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-store text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-600">Belum ada UMKM unggulan</p>
            </div>
        @endif
    </div>
</section>

<!-- Map Section -->
<section class="py-12 sm:py-16 lg:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 sm:mb-10 lg:mb-12 animate-on-scroll">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 mb-3 sm:mb-4">🗺️ Peta Wilayah RT</h2>
            <p class="text-base sm:text-lg lg:text-xl text-gray-600">Lokasi dan batas wilayah {{ $profileData['rt_name'] }}</p>
        </div>

        <!-- Interactive Map -->
        <div class="mb-8 lg:mb-12">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden animate-on-scroll">
                <div class="relative">
                    <div id="rt-interactive-map" class="w-full h-96 rounded-xl"></div>
                    <div class="absolute top-4 left-4 z-[1000]">
                        <span class="bg-green-600/95 text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-lg backdrop-blur-sm">
                            <i class="fas fa-satellite-dish mr-1"></i>Live Map
                        </span>
                    </div>
                    <div class="absolute bottom-4 right-4 z-[1000] hidden md:flex gap-2">
                        <a href="https://www.google.com/maps/place/-7.484139,112.654531" target="_blank" rel="noopener"
                           class="bg-white/95 hover:bg-white text-green-700 hover:text-green-800 text-xs font-medium px-3 py-2 rounded-lg shadow-lg flex items-center gap-1 transition-all duration-200 backdrop-blur-sm">
                            <i class="fas fa-external-link-alt"></i> Google Maps
                        </a>
                    </div>
                    <div class="absolute top-4 right-4 z-[1000] md:hidden">
                        <button id="fullscreen-btn" class="bg-white/95 hover:bg-white text-gray-700 hover:text-green-600 p-2 rounded-lg shadow-lg transition-all duration-200 backdrop-blur-sm">
                            <i class="fas fa-expand text-sm"></i>
                        </button>
                    </div>
                    <noscript>
                        <div class="p-6 text-center bg-gray-50 rounded-xl">
                            <i class="fas fa-map-marked-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-gray-700 mb-3 font-medium">JavaScript dinonaktifkan untuk peta interaktif.</p>
                            <a href="https://www.google.com/maps/place/-7.484139,112.654531" target="_blank" rel="noopener"
                               class="inline-flex items-center text-green-600 hover:text-green-700 font-semibold">
                                <i class="fas fa-external-link-alt mr-2"></i>Buka peta di Google Maps
                            </a>
                        </div>
                    </noscript>
                </div>
            </div>
        </div>

        <!-- Map Information Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 animate-on-scroll">
            <!-- Location Info -->
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-5 lg:p-6">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-3 sm:mb-4">
                    <i class="fas fa-map-marker-alt text-green-600 mr-2"></i>Informasi Lokasi
                </h3>
                <div class="space-y-2 sm:space-y-3">
                    <div>
                        <p class="font-semibold text-gray-700 text-sm sm:text-base">Alamat</p>
                        <p class="text-gray-600 text-xs sm:text-sm">{{ $profileData['address'] }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-700 text-sm sm:text-base">Koordinat</p>
                        <p class="text-gray-600 text-xs sm:text-sm">-7.484139, 112.654531</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-700 text-sm sm:text-base">Luas Wilayah</p>
                        <p class="text-gray-600 text-xs sm:text-sm">± 2.5 Hektar</p>
                    </div>
                </div>
            </div>

            <!-- Landmarks -->
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-5 lg:p-6">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-3 sm:mb-4">
                    <i class="fas fa-landmark text-green-600 mr-2"></i>Landmark Terdekat
                </h3>
                <div class="space-y-2 sm:space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-mosque text-green-600 w-4 sm:w-5 mr-2 sm:mr-3"></i>
                        <span class="text-gray-600 text-xs sm:text-sm">Masjid Al-Ikhlas (100m)</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-school text-green-600 w-4 sm:w-5 mr-2 sm:mr-3"></i>
                        <span class="text-gray-600 text-xs sm:text-sm">SD Negeri 05 (300m)</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-hospital text-red-600 w-4 sm:w-5 mr-2 sm:mr-3"></i>
                        <span class="text-gray-600 text-xs sm:text-sm">Puskesmas Sukamaju (500m)</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-shopping-cart text-orange-600 w-4 sm:w-5 mr-2 sm:mr-3"></i>
                        <span class="text-gray-600 text-xs sm:text-sm">Pasar Tradisional (800m)</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-bus text-orange-600 w-4 sm:w-5 mr-2 sm:mr-3"></i>
                        <span class="text-gray-600 text-xs sm:text-sm">Halte TransJakarta (1.2km)</span>
                    </div>
                </div>
            </div>

            <!-- Transportation -->
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-5 lg:p-6">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-3 sm:mb-4">
                    <i class="fas fa-route text-orange-600 mr-2"></i>Akses Transportasi
                </h3>
                <div class="space-y-2 sm:space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-car text-green-600 w-4 sm:w-5 mr-2 sm:mr-3"></i>
                        <span class="text-gray-600 text-xs sm:text-sm">Akses jalan utama 24 jam</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-motorcycle text-green-600 w-4 sm:w-5 mr-2 sm:mr-3"></i>
                        <span class="text-gray-600 text-xs sm:text-sm">Ojek online tersedia</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-bus text-red-600 w-4 sm:w-5 mr-2 sm:mr-3"></i>
                        <span class="text-gray-600 text-xs sm:text-sm">Angkot & Bus kota</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-12 sm:py-16 lg:py-20 hero-gradient">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8 animate-on-scroll">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-4 sm:mb-5 lg:mb-6">
            Bergabung dengan Sistem RT Digital
        </h2>
        <p class="text-base sm:text-lg lg:text-xl text-green-100 mb-6 sm:mb-7 lg:mb-8">
            Nikmati kemudahan akses informasi pengumuman, dukungan UMKM warga, dan layanan RT dalam satu platform
        </p>
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
            <a href="{{ route('login') }}" class="bg-white text-green-600 px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors text-sm sm:text-base">
                <i class="fas fa-sign-in-alt mr-2"></i>Masuk Sebagai Warga
            </a>
            <a href="{{ route('landing.announcements') }}" class="border-2 border-white text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:bg-white hover:text-green-600 transition-colors text-sm sm:text-base">
                <i class="fas fa-info-circle mr-2"></i>Pelajari Lebih Lanjut
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Interactive Map
    const mapEl = document.getElementById('rt-interactive-map');
    if (mapEl) {
        // Base tile layers
        const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
        });

        const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Imagery &copy; <a href="https://www.esri.com/" target="_blank">Esri</a> &mdash; Source: Esri, Maxar, Earthstar Geographics'
        });

        // Initialize map with enhanced options
        const map = L.map('rt-interactive-map', {
            center: [-7.484139, 112.654531],
            zoom: 17,
            minZoom: 10,
            maxZoom: 20,
            layers: [osmLayer],
            zoomControl: false,
            scrollWheelZoom: true,
            doubleClickZoom: true,
            dragging: true,
            touchZoom: true,
            boxZoom: true,
            keyboard: true,
            attributionControl: true
        });

        // Custom zoom control with better positioning
        L.control.zoom({
            position: 'topright',
            zoomInText: '+',
            zoomOutText: '−',
            zoomInTitle: 'Zoom in',
            zoomOutTitle: 'Zoom out'
        }).addTo(map);

        // Enhanced layer control
        const baseLayers = {
            '<i class="fas fa-road mr-1"></i> Street View': osmLayer,
            '<i class="fas fa-satellite mr-1"></i> Satellite': satelliteLayer
        };

        L.control.layers(baseLayers, {}, {
            position: 'topleft',
            collapsed: false
        }).addTo(map);

        // RT boundary polygon with better styling
        const boundaryCoords = [
            [-7.48460, 112.65410],
            [-7.48460, 112.65495],
            [-7.48370, 112.65495],
            [-7.48370, 112.65410],
            [-7.48460, 112.65410] // Close the polygon
        ];

        const boundary = L.polygon(boundaryCoords, {
            color: '#16a34a',
            weight: 3,
            opacity: 0.8,
            fillColor: '#16a34a',
            fillOpacity: 0.2,
            smoothFactor: 1.0
        }).addTo(map);

        boundary.bindTooltip('Batas Wilayah RT 02/RW 04', {
            permanent: false,
            direction: 'center',
            className: 'custom-tooltip'
        });

        // Main RT marker with custom icon
        const rtIcon = L.divIcon({
            className: 'custom-rt-marker',
            html: '<div style="background: #16a34a; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
            iconSize: [26, 26],
            iconAnchor: [13, 13]
        });

        L.marker([-7.484139, 112.654531], { icon: rtIcon }).addTo(map)
            .bindPopup(`
                <div class="text-center p-2">
                    <h4 class="font-bold text-green-700 mb-1">🏠 Kantor RT 02/RW 04</h4>
                    <p class="text-sm text-gray-600">Kedurus - Kepatihan</p>
                    <p class="text-xs text-gray-500 mt-1">📍 -7.484139, 112.654531</p>
                </div>
            `, { maxWidth: 200 });

        // Landmark markers with icons
        const landmarks = [
            { coords: [-7.484000, 112.654300], label: 'Masjid Al-Ikhlas', icon: '🕌', color: '#059669' },
            { coords: [-7.484250, 112.654800], label: 'SD Negeri 05', icon: '🏫', color: '#2563eb' },
            { coords: [-7.484450, 112.654500], label: 'Puskesmas Sukamaju', icon: '🏥', color: '#dc2626' },
            { coords: [-7.483950, 112.654900], label: 'Pasar Tradisional', icon: '🏪', color: '#ea580c' },
            { coords: [-7.483800, 112.654650], label: 'Halte TransJakarta', icon: '🚌', color: '#7c2d12' }
        ];

        landmarks.forEach(landmark => {
            const landmarkIcon = L.divIcon({
                className: 'custom-landmark-marker',
                html: `<div style="background: ${landmark.color}; color: white; padding: 4px 8px; border-radius: 8px; font-size: 12px; font-weight: bold; box-shadow: 0 2px 6px rgba(0,0,0,0.3); border: 2px solid white;">${landmark.icon}</div>`,
                iconSize: [30, 20],
                iconAnchor: [15, 10]
            });

            L.marker(landmark.coords, { icon: landmarkIcon }).addTo(map)
                .bindPopup(`
                    <div class="text-center p-2">
                        <h4 class="font-bold mb-1">${landmark.icon} ${landmark.label}</h4>
                        <p class="text-xs text-gray-500">📍 ${landmark.coords[0].toFixed(6)}, ${landmark.coords[1].toFixed(6)}</p>
                    </div>
                `, { maxWidth: 180 });
        });

        // UMKM markers with circle style
        const umkmBusinesses = [
            { coords: [-7.48410, 112.65420], name: 'Warung Bu Sari', category: 'Kuliner' },
            { coords: [-7.48430, 112.65440], name: 'Laundry Express', category: 'Jasa' },
            { coords: [-7.48420, 112.65475], name: 'Kedai Kopi RT', category: 'Kuliner' },
            { coords: [-7.48405, 112.65460], name: 'Toko Kelontong Barokah', category: 'Retail' }
        ];

        umkmBusinesses.forEach(business => {
            L.circleMarker(business.coords, {
                radius: 8,
                color: '#2563eb',
                weight: 3,
                fillColor: '#3b82f6',
                fillOpacity: 0.8
            }).addTo(map)
            .bindPopup(`
                <div class="p-2">
                    <h4 class="font-bold text-blue-700 mb-1">🛍️ ${business.name}</h4>
                    <p class="text-xs text-gray-600 mb-1">Kategori: ${business.category}</p>
                    <p class="text-xs text-gray-500">UMKM Warga RT</p>
                </div>
            `, { maxWidth: 180 });
        });

        // Fullscreen functionality for mobile
        const fullscreenBtn = document.getElementById('fullscreen-btn');
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', function() {
                if (mapEl.requestFullscreen) {
                    mapEl.requestFullscreen();
                } else if (mapEl.webkitRequestFullscreen) {
                    mapEl.webkitRequestFullscreen();
                } else if (mapEl.msRequestFullscreen) {
                    mapEl.msRequestFullscreen();
                }
                setTimeout(() => map.invalidateSize(), 100);
            });
        }

        // Auto-fit bounds to show all markers with padding
        const allFeatures = [
            boundary,
            ...landmarks.map(l => L.marker(l.coords)),
            ...umkmBusinesses.map(u => L.circleMarker(u.coords))
        ];
        const group = new L.FeatureGroup(allFeatures);
        map.fitBounds(group.getBounds().pad(0.1));

        // Map loading event handlers
        map.on('load', function() {
            console.log('🗺️ Interactive map loaded successfully');
        });

        map.on('moveend', function() {
            const center = map.getCenter();
            console.log(`Map center: ${center.lat.toFixed(6)}, ${center.lng.toFixed(6)}`);
        });

        // Add custom CSS for tooltips and popups
        const style = document.createElement('style');
        style.textContent = `
            .custom-tooltip {
                background: rgba(22, 163, 74, 0.9) !important;
                color: white !important;
                border: none !important;
                border-radius: 6px !important;
                font-size: 12px !important;
                font-weight: 500 !important;
                padding: 4px 8px !important;
            }
            .leaflet-popup-content {
                margin: 8px 12px !important;
                line-height: 1.4 !important;
            }
            .custom-rt-marker, .custom-landmark-marker {
                background: transparent !important;
                border: none !important;
            }
        `;
        document.head.appendChild(style);
    }

    // Real-time stats updates
    function updateLandingStats() {
        fetch('/api/landing/stats')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateStatElement('stat-households', data.data.profile.total_households);
                    updateStatElement('stat-residents', data.data.profile.total_residents);
                    updateStatElement('stat-businesses', data.data.profile.total_businesses);
                    updateStatElement('stat-announcements', data.data.recent_announcements);
                }
            })
            .catch(error => console.error('Error updating stats:', error));
    }

    function updateStatElement(elementId, value) {
        const el = document.getElementById(elementId);
        if (!el) return;
        el.classList.add('stat-update');
        el.textContent = value;
        setTimeout(() => el.classList.remove('stat-update'), 600);
    }

    // Add real-time indicator
    const heroSection = document.querySelector('.hero-gradient h1');
    if (heroSection) {
        const indicator = document.createElement('span');
        indicator.className = 'real-time-indicator';
        indicator.title = 'Data diperbarui secara real-time';
        heroSection.appendChild(indicator);
    }

    // Auto refresh every 45 seconds
    setInterval(updateLandingStats, 45000);

    // Update on page visibility change
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            setTimeout(updateLandingStats, 1000);
        }
    });
});
</script>
@endpush
