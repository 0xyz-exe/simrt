@extends('layouts.mobile')

@section('title', 'Dashboard')

@section('content')
<div class="p-4 space-y-6" x-data="dashboardData()">
    <!-- Pull to Refresh -->
    <div x-show="pullToRefresh" class="pull-to-refresh text-center py-4">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div>
        <p class="text-sm text-gray-600 mt-2">Memuat data terbaru...</p>
    </div>

    <!-- Stats Cards -->
    <div class="mobile-grid">
        <!-- Total Households -->
        <div class="bg-white rounded-xl shadow-sm p-6 card-slide-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total KK</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.households"></p>
                    <p class="text-xs text-green-600 mt-1">+2 bulan ini</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Residents -->
        <div class="bg-white rounded-xl shadow-sm p-6 card-slide-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Warga</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.residents"></p>
                    <p class="text-xs text-green-600 mt-1">+5 bulan ini</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Letters -->
        <div class="bg-white rounded-xl shadow-sm p-6 card-slide-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Surat Pending</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.pendingLetters"></p>
                    <p class="text-xs text-yellow-600 mt-1">Butuh perhatian</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Unpaid Dues -->
        <div class="bg-white rounded-xl shadow-sm p-6 card-slide-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Iuran Belum Bayar</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.unpaidDues"></p>
                    <p class="text-xs text-red-600 mt-1">Perlu ditagih</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('admin.letters.create') }}"
               class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors btn-touch">
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900">Buat Surat</span>
            </a>

            <a href="{{ route('admin.residents.create') }}"
               class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors btn-touch">
                <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900">Tambah Warga</span>
            </a>

            <a href="{{ route('admin.dues.create') }}"
               class="flex flex-col items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors btn-touch">
                <div class="w-12 h-12 bg-yellow-600 rounded-lg flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900">Buat Iuran</span>
            </a>

            <a href="{{ route('admin.reports') }}"
               class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors btn-touch">
                <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2-2z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900">Laporan</span>
            </a>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
            <a href="{{ route('admin.activities') }}" class="text-sm text-blue-600 hover:text-blue-800">Lihat Semua</a>
        </div>

        <div class="space-y-4">
            <template x-for="activity in recentActivities" :key="activity.id">
                <div class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900" x-text="activity.description"></p>
                        <p class="text-xs text-gray-500" x-text="activity.time_ago"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Collection Chart (Mobile Optimized) -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Tingkat Pengumpulan Iuran</h3>
            <select class="text-sm border-gray-300 rounded-md" x-model="selectedMonth" @change="updateChart()">
                <option value="1">Januari</option>
                <option value="2">Februari</option>
                <option value="3">Maret</option>
                <option value="4">April</option>
                <option value="5">Mei</option>
                <option value="6">Juni</option>
                <option value="7">Juli</option>
                <option value="8">Agustus</option>
                <option value="9">September</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">Desember</option>
            </select>
        </div>

        <div class="space-y-4">
            <!-- Collection Rate -->
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-600">Tingkat Pengumpulan</span>
                    <span class="font-medium" x-text="collectionRate + '%'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-500"
                         :style="`width: ${collectionRate}%`"></div>
                </div>
            </div>

            <!-- Payment Status Breakdown -->
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="p-3 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600" x-text="paymentStats.paid"></p>
                    <p class="text-xs text-green-600">Lunas</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-lg">
                    <p class="text-2xl font-bold text-yellow-600" x-text="paymentStats.partial"></p>
                    <p class="text-xs text-yellow-600">Sebagian</p>
                </div>
                <div class="p-3 bg-red-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-600" x-text="paymentStats.unpaid"></p>
                    <p class="text-xs text-red-600">Belum Bayar</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function dashboardData() {
    return {
        pullToRefresh: false,
        selectedMonth: new Date().getMonth() + 1,
        stats: {
            households: {{ $stats['households'] ?? 0 }},
            residents: {{ $stats['residents'] ?? 0 }},
            pendingLetters: {{ $stats['pendingLetters'] ?? 0 }},
            unpaidDues: {{ $stats['unpaidDues'] ?? 0 }}
        },
        collectionRate: {{ $collectionRate ?? 75 }},
        paymentStats: {
            paid: {{ $paymentStats['paid'] ?? 15 }},
            partial: {{ $paymentStats['partial'] ?? 3 }},
            unpaid: {{ $paymentStats['unpaid'] ?? 7 }}
        },
        recentActivities: @json($recentActivities ?? [
            ['id' => 1, 'description' => 'Surat domisili untuk John Doe disetujui', 'time_ago' => '2 menit lalu'],
            ['id' => 2, 'description' => 'Pembayaran iuran dari keluarga Budi diterima', 'time_ago' => '1 jam lalu'],
            ['id' => 3, 'description' => 'Warga baru Sarah terdaftar', 'time_ago' => '3 jam lalu']
        ]),

        init() {
            this.setupPullToRefresh();
        },

        setupPullToRefresh() {
            let startY = 0;
            let currentY = 0;
            let pulling = false;

            document.addEventListener('touchstart', (e) => {
                if (window.scrollY === 0) {
                    startY = e.touches[0].clientY;
                    pulling = true;
                }
            });

            document.addEventListener('touchmove', (e) => {
                if (pulling) {
                    currentY = e.touches[0].clientY;
                    const pullDistance = currentY - startY;

                    if (pullDistance > 100 && !this.pullToRefresh) {
                        this.pullToRefresh = true;
                        this.refreshData();
                    }
                }
            });

            document.addEventListener('touchend', () => {
                pulling = false;
                setTimeout(() => {
                    this.pullToRefresh = false;
                }, 2000);
            });
        },

        async refreshData() {
            try {
                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 2000));

                // Update stats (in real app, fetch from API)
                this.stats = {
                    households: Math.floor(Math.random() * 100) + 20,
                    residents: Math.floor(Math.random() * 300) + 50,
                    pendingLetters: Math.floor(Math.random() * 10) + 1,
                    unpaidDues: Math.floor(Math.random() * 20) + 5
                };

                this.showToast('Data berhasil diperbarui', 'success');
            } catch (error) {
                this.showToast('Gagal memperbarui data', 'error');
            }
        },

        updateChart() {
            // Update collection data based on selected month
            this.collectionRate = Math.floor(Math.random() * 40) + 60;
            this.paymentStats = {
                paid: Math.floor(Math.random() * 20) + 10,
                partial: Math.floor(Math.random() * 5) + 1,
                unpaid: Math.floor(Math.random() * 10) + 2
            };
        },

        showToast(message, type) {
            // Use the global toast function from the layout
            if (window.showToast) {
                window.showToast(message, type);
            }
        }
    }
}
</script>
@endsection
