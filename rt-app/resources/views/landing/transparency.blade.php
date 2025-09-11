@extends('layouts.landing')

@section('title', 'Transparansi Iuran - Guyub Rukun Warga RT 02/RW 04 Kedurus - Kepatihan Kedurus Kepatihan')
@section('description', 'Laporan transparansi pengelolaan iuran RT 02/RW 04 Kedurus - Kepatihan Kedurus Kepatihan dengan data yang akurat dan terbuka untuk seluruh warga')

@section('extra_css')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<!-- Header Section -->
<section class="hero-gradient py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                <i class="fas fa-chart-pie mr-3"></i>Transparansi Iuran
            </h1>
            <p class="text-xl opacity-90 max-w-2xl mx-auto">
                Keterbukaan pengelolaan iuran RT 02/RW 04 Kedurus - Kepatihan  untuk kepercayaan dan akuntabilitas
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
            <li class="text-gray-900 font-medium">Transparansi</li>
        </ol>
    </div>
</nav>

<!-- Current Month Summary -->
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Laporan Bulan {{ $currentMonthData['month'] }}</h2>
            <p class="text-xl text-gray-600">Ringkasan pengumpulan iuran bulan ini</p>
        </div>

        <div class="grid md:grid-cols-4 gap-8 mb-12">
            <!-- Total Iuran -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-coins text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">
                    Rp {{ number_format($currentMonthData['total_dues'], 0, ',', '.') }}
                </h3>
                <p class="text-gray-600">Total Iuran</p>
            </div>

            <!-- Terkumpul -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">
                    Rp {{ number_format($currentMonthData['collected_amount'], 0, ',', '.') }}
                </h3>
                <p class="text-gray-600">Terkumpul</p>
            </div>

            <!-- Persentase -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-percentage text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">
                    {{ $currentMonthData['collection_percentage'] }}%
                </h3>
                <p class="text-gray-600">Persentase</p>
            </div>

            <!-- Status Pembayaran -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-orange-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">
                    {{ $currentMonthData['paid_count'] }}/{{ $currentMonthData['paid_count'] + $currentMonthData['pending_count'] }}
                </h3>
                <p class="text-gray-600">Sudah Bayar</p>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Progress Pengumpulan Iuran</h3>
            <div class="w-full bg-gray-200 rounded-full h-4 mb-4">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-4 rounded-full transition-all duration-1000"
                     style="width: {{ $currentMonthData['collection_percentage'] }}%"></div>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <span>0%</span>
                <span class="font-semibold">{{ $currentMonthData['collection_percentage'] }}%</span>
                <span>100%</span>
            </div>
        </div>
    </div>
</section>

<!-- Summary Data -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Rangkuman Keuangan RT</h2>
            <p class="text-xl text-gray-600">Informasi keuangan dan aktivitas RT bulan ini</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid md:grid-cols-3 gap-8 mb-12">
            <!-- Total Iuran -->
            <div class="rt-gradient rounded-xl shadow-lg p-8 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold">Total Iuran</h3>
                    <i class="fas fa-coins text-3xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold mb-2">Rp {{ number_format($currentMonthData['total_dues'], 0, ',', '.') }}</div>
                <p class="opacity-90">Target bulan {{ $currentMonthData['month'] }}</p>
            </div>

            <!-- Terkumpul -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-8 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold">Sudah Terkumpul</h3>
                    <i class="fas fa-check-circle text-3xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold mb-2">Rp {{ number_format($currentMonthData['collected_amount'], 0, ',', '.') }}</div>
                <p class="opacity-90">Dari {{ $currentMonthData['paid_count'] }} KK</p>
            </div>

            <!-- Belum Bayar -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-8 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold">Belum Bayar</h3>
                    <i class="fas fa-hourglass-half text-3xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold mb-2">{{ $currentMonthData['pending_count'] }}</div>
                <p class="opacity-90">Rumah Tangga</p>
            </div>
        </div>

        <!-- Monthly Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-800">Detail Per Bulan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Bulan</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Total Iuran</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Terkumpul</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Persentase</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Sudah Bayar</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($transparencyData as $data)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                {{ $data['month'] }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 text-right">
                                Rp {{ number_format($data['total_dues'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 text-right">
                                Rp {{ number_format($data['collected_amount'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    @if($data['collection_percentage'] >= 90) bg-green-100 text-green-800
                                    @elseif($data['collection_percentage'] >= 70) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $data['collection_percentage'] }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 text-center">
                                {{ $data['paid_count'] }}/{{ $data['total_count'] }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($data['collection_percentage'] >= 90)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Excellent
                                    </span>
                                @elseif($data['collection_percentage'] >= 70)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-exclamation-circle mr-1"></i>Good
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>Needs Attention
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Commitment Section -->
<section class="py-16 hero-gradient">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Komitmen Transparansi
        </h2>
        <p class="text-xl text-green-100 mb-8">
            Kami berkomitmen untuk menjaga transparansi dalam pengelolaan iuran RT demi kepercayaan dan akuntabilitas kepada seluruh warga
        </p>
        <div class="grid md:grid-cols-3 gap-8 text-white">
            <div class="text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-eye text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2">Transparan</h3>
                <p class="text-green-100">Data keuangan terbuka untuk semua warga</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2">Real-time</h3>
                <p class="text-green-100">Update data secara berkala dan tepat waktu</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2">Akuntabel</h3>
                <p class="text-green-100">Pertanggungjawaban setiap rupiah yang dikelola</p>
            </div>
        </div>
    </div>
</section>
@endsection

@section('extra_js')
<script>
// Simple animations for transparency page
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bar on page load
    const progressBar = document.querySelector('.bg-gradient-to-r');
    if (progressBar) {
        progressBar.style.width = '0%';
        setTimeout(() => {
            progressBar.style.width = '{{ $currentMonthData["collection_percentage"] }}%';
        }, 500);
    }

    // Animate numbers counting up
    const animateNumber = (element, target) => {
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current).toLocaleString('id-ID');
        }, 30);
    };

    // Find and animate summary cards
    const summaryCards = document.querySelectorAll('.text-3xl.font-bold');
    summaryCards.forEach((card, index) => {
        if (card.textContent.includes('Rp')) {
            // Extract number from currency format
            const number = parseInt(card.textContent.replace(/[^\d]/g, ''));
            if (number > 0) {
                setTimeout(() => {
                    card.textContent = 'Rp 0';
                    animateNumber(card, number);
                }, index * 200);
            }
        }
    });

    console.log('Transparency page animations loaded');
});
</script>
@endsection
