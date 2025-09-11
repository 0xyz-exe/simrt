@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="mb-6">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ url('/') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>

        <!-- Header Section -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">Dashboard</h1>
                <p class="text-slate-600">Selamat datang, {{ auth()->user()->name }}</p>
            </div>

            <!-- Refresh Button -->
            <button id="refresh-btn" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Perbarui Data
            </button>
        </div>
    </div>

    <!-- Last Refresh Time -->
    <div class="mb-4">
        <p id="last-refresh" class="text-sm text-gray-500"></p>
    </div>

    @can('isAdmin')
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Stats Cards -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">Total Keluarga</h3>
                <p id="total-households" class="text-3xl font-bold text-blue-600">{{ \App\Models\Household::count() }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">Total Warga</h3>
                <p id="total-residents" class="text-3xl font-bold text-green-600">{{ \App\Models\Resident::count() }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">Iuran Bulan Ini</h3>
                @php
                    $currentMonth = date('n');
                    $currentYear = date('Y');
                    $totalDues = \App\Models\Due::where('month', $currentMonth)->where('year', $currentYear)->count();
                    $paidDues = \App\Models\Due::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'paid')->count();
                    $percentage = $totalDues > 0 ? round(($paidDues / $totalDues) * 100) : 0;
                @endphp
                <p id="dues-percentage" class="text-3xl font-bold text-yellow-600">{{ $percentage }}%</p>
                <p class="text-sm text-slate-500 mb-3">{{ $paidDues }}/{{ $totalDues }} lunas</p>

                <!-- Mini Progress Bar -->
                <div class="w-full bg-slate-200 rounded-full h-2">
                    <div id="dues-progress" class="bg-gradient-to-r from-yellow-400 to-orange-500 h-2 rounded-full transition-all duration-500"
                         style="width: {{ $percentage }}%"></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">Surat Pending</h3>
                @php
                    $pendingLetters = \App\Models\Letter::where('status', 'pending')->count();
                    $totalLetters = \App\Models\Letter::count();
                    $pendingPercentage = $totalLetters > 0 ? round(($pendingLetters / $totalLetters) * 100) : 0;
                @endphp
                <p id="pending-letters" class="text-3xl font-bold text-purple-600">{{ $pendingLetters }}</p>
                <p class="text-sm text-slate-500 mb-3">dari {{ $totalLetters }} total</p>

                <!-- Mini Progress Bar -->
                <div class="w-full bg-slate-200 rounded-full h-2">
                    <div id="letters-progress" class="bg-gradient-to-r from-purple-400 to-pink-500 h-2 rounded-full transition-all duration-500"
                         style="width: {{ $pendingPercentage }}%"></div>
                </div>

                <!-- Alert indicator -->
                @if($pendingLetters > 0)
                    <div class="flex items-center text-xs text-amber-600">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Perlu diproses
                    </div>
                @else
                    <div class="flex items-center text-xs text-green-600">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Semua terproses
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Summary -->
        <div class="mb-8 grid md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Ringkasan Hari Ini</h3>
                <div class="space-y-3">
                    @php
                        $todayPayments = \App\Models\Payment::whereDate('payment_date', today())->count();
                        $todayLetters = \App\Models\Letter::whereDate('created_at', today())->count();
                        $thisMonthPayments = \App\Models\Payment::whereMonth('payment_date', now()->month)->sum('amount');
                    @endphp
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">💰 Pembayaran Hari Ini</span>
                        <span class="font-bold text-green-600">{{ $todayPayments }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">📝 Pengajuan Surat Hari Ini</span>
                        <span class="font-bold text-purple-600">{{ $todayLetters }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">💵 Total Terkumpul Bulan Ini</span>
                        <span class="font-bold text-blue-600">Rp {{ number_format($thisMonthPayments, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Status Sistem</h3>
                <div class="space-y-3">
                    @php
                        $unpaidCount = \App\Models\Due::where('status', 'unpaid')->count();
                        $pendingCount = \App\Models\Letter::where('status', 'pending')->count();
                        $activeAnnouncements = \App\Models\Announcement::count();
                    @endphp
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">⚠️ Iuran Belum Lunas</span>
                        <span class="font-bold {{ $unpaidCount > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $unpaidCount }} keluarga
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">🕐 Surat Pending</span>
                        <span class="font-bold {{ $pendingCount > 0 ? 'text-yellow-600' : 'text-green-600' }}">
                            {{ $pendingCount }} pengajuan
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">📢 Pengumuman Aktif</span>
                        <span class="font-bold text-blue-600">{{ $activeAnnouncements }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Menu -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold">Menu Admin</h2>
                <a href="{{ route('dashboard.analytics') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    📊 Lihat Analytics Detail
                </a>
            </div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="{{ route('announcements.index') }}" class="block bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                <h3 class="text-lg font-semibold mb-2">Kelola Pengumuman</h3>
                <p class="text-slate-600">Buat dan kelola pengumuman untuk warga</p>
            </a>

            <a href="{{ route('activities.index') }}" class="block bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                <h3 class="text-lg font-semibold mb-2">Kelola Kegiatan</h3>
                <p class="text-slate-600">Buat dan kelola kegiatan RT</p>
            </a>

            <a href="{{ route('admin.businesses.index') }}" class="block bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                <h3 class="text-lg font-semibold mb-2">Kelola UMKM</h3>
                <p class="text-slate-600">Manage usaha mikro warga RT</p>
            </a>

            <a href="{{ route('households.index') }}" class="block bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                <h3 class="text-lg font-semibold mb-2">Data Keluarga</h3>
                <p class="text-slate-600">Kelola data keluarga dan warga</p>
            </a>

            <a href="{{ route('residents.index') }}" class="block bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                <h3 class="text-lg font-semibold mb-2">Data Warga</h3>
                <p class="text-slate-600">Kelola data individual warga RT</p>
            </a>

            <a href="{{ route('dues.index') }}" class="block bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                <h3 class="text-lg font-semibold mb-2">Iuran & Pembayaran</h3>
                <p class="text-slate-600">Generate iuran dan catat pembayaran</p>
            </a>

            <a href="{{ route('letters.index') }}" class="block bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                <h3 class="text-lg font-semibold mb-2">Persetujuan Surat</h3>
                <p class="text-slate-600">Approve/reject pengajuan surat warga</p>
            </a>

            <a href="{{ route('settings.index') }}" class="block bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                <h3 class="text-lg font-semibold mb-2">Pengaturan Website</h3>
                <p class="text-slate-600">Kelola konten dan pengaturan sistem</p>
            </a>
        </div>
    @else
        <!-- Resident Menu -->
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Data Keluarga Saya</h3>
                <p class="text-slate-600 mb-4">Lihat data keluarga yang terdaftar</p>
                <a href="{{ route('households.mine') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Lihat Data
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Iuran Saya</h3>
                <p class="text-slate-600 mb-4">Cek status iuran bulanan</p>
                <a href="{{ route('dues.mine') }}" class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Cek Iuran
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Ajukan Surat</h3>
                <p class="text-slate-600 mb-4">Pengajuan surat domisili atau pengantar</p>
                <a href="{{ route('letters.create') }}" class="inline-block px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                    Ajukan
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Riwayat Surat</h3>
                <p class="text-slate-600 mb-4">Lihat status dan download surat</p>
                <a href="{{ route('letters.mine') }}" class="inline-block px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
                    Lihat Riwayat
                </a>
            </div>
        </div>
    @endcan

    <!-- Real-time Update Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Real-time dashboard updates
        function updateDashboardStats() {
            fetch('/api/dashboard/stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update stats cards
                        updateStatsCard('total-households', data.data.stats.total_households);
                        updateStatsCard('total-residents', data.data.stats.total_residents);
                        updateStatsCard('dues-percentage', data.data.dues.dues_percentage + '%');
                        updateStatsCard('pending-letters', data.data.stats.pending_letters);

                        // Update progress bars
                        updateProgressBar('dues-progress', data.data.dues.dues_percentage);
                        updateProgressBar('letters-progress', data.data.dues.pending_percentage);

                        // Update last refresh time
                        updateLastRefresh();

                        console.log('Dashboard stats updated successfully');
                    }
                })
                .catch(error => {
                    console.error('Error updating dashboard stats:', error);
                });
        }

        function updateStatsCard(elementId, value) {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = value;
                element.classList.add('text-pulse');
                setTimeout(() => element.classList.remove('text-pulse'), 1000);
            }
        }

        function updateProgressBar(elementId, percentage) {
            const element = document.getElementById(elementId);
            if (element) {
                element.style.width = percentage + '%';
                element.classList.add('bg-pulse');
                setTimeout(() => element.classList.remove('bg-pulse'), 1000);
            }
        }

        function updateLastRefresh() {
            const element = document.getElementById('last-refresh');
            if (element) {
                element.textContent = 'Terakhir diperbarui: ' + new Date().toLocaleTimeString('id-ID');
            }
        }

        // Auto refresh every 30 seconds
        setInterval(updateDashboardStats, 30000);

        // Manual refresh button
        const refreshBtn = document.getElementById('refresh-btn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                this.disabled = true;
                this.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Memperbarui...';

                updateDashboardStats();

                setTimeout(() => {
                    this.disabled = false;
                    this.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Perbarui Data';
                }, 2000);
            });
        }

        // Add CSS for animations
        const style = document.createElement('style');
        style.textContent = `
            .text-pulse {
                animation: textPulse 1s ease-in-out;
            }
            .bg-pulse {
                animation: bgPulse 1s ease-in-out;
            }
            @keyframes textPulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); color: #3b82f6; }
            }
            @keyframes bgPulse {
                0%, 100% { transform: scaleX(1); }
                50% { transform: scaleX(1.02); }
            }
        `;
        document.head.appendChild(style);

        // Initial load
        updateLastRefresh();
    });
    </script>
@endsection
