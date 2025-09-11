@extends('layouts.landing')
@section('title', 'Guyub Rukun Warga - RT 02/RW 04 Kedurus - Kepatihan Kedurus Kepatihan')
@section('content')
<!-- Hero Section -->
<section class="hero-gradient text-white py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center animate-fade-in">
            <div class="mb-8 flex justify-center">
                <img src="{{ asset('images/logo-rt.png') }}" alt="Logo RT" class="w-24 h-24 sm:w-32 sm:h-32 mb-6">
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-6">
                Guyub Rukun Warga
            </h1>
            <p class="text-lg sm:text-xl mb-4 opacity-90">
                RT 02/RW 04
            </p>
            <p class="text-base sm:text-lg mb-8 opacity-80 max-w-3xl mx-auto">
                Membangun persatuan dan kebersamaan warga untuk menciptakan lingkungan yang nyaman, aman, dan sejahtera bagi seluruh keluarga.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-white text-rt-green font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk Portal Warga
                </a>
                <a href="{{ route('landing.businesses') }}" class="inline-flex items-center px-6 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-rt-green transition-colors">
                    <i class="fas fa-store mr-2"></i>
                    Lihat UMKM
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-2xl sm:text-3xl font-bold text-rt-green">{{ $statistics['households'] ?? 6 }}</div>
                <div class="text-sm text-gray-600">Rumah Tangga</div>
            </div>
            <div class="text-center">
                <div class="text-2xl sm:text-3xl font-bold text-rt-green">{{ $statistics['residents'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Warga</div>
            </div>
            <div class="text-center">
                <div class="text-2xl sm:text-3xl font-bold text-rt-green">{{ $statistics['active_businesses'] ?? 5 }}</div>
                <div class="text-sm text-gray-600">UMKM Aktif</div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Layanan Portal Warga</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Akses berbagai layanan dan informasi RT dengan mudah dan transparan
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-md card-hover">
                <div class="w-12 h-12 rt-gradient rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-store text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">UMKM</h3>
                <p class="text-gray-600 mb-4">Dukung usaha mikro warga RT untuk ekonomi yang lebih kuat</p>
                <a href="{{ route('landing.businesses') }}" class="text-rt-green font-medium hover:underline">
                    Lihat UMKM →
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md card-hover">
                <div class="w-12 h-12 rt-gradient rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-chart-pie text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Transparansi</h3>
                <p class="text-gray-600 mb-4">Laporan keuangan dan kegiatan RT yang transparan</p>
                <a href="{{ route('landing.transparency') }}" class="text-rt-green font-medium hover:underline">
                    Lihat Laporan →
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md card-hover">
                <div class="w-12 h-12 rt-gradient rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Portal Warga</h3>
                <p class="text-gray-600 mb-4">Akses khusus warga untuk berbagai layanan digital</p>
                <a href="{{ route('login') }}" class="text-rt-green font-medium hover:underline">
                    Masuk Portal →
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
