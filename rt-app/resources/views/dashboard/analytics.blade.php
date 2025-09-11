@extends('layouts.app')
@section('title', 'Dashboard Analytics')

@push('styles')
<style>
    @media print {
        /* Hide elements that shouldn't be printed */
        .no-print,
        .print-hide,
        header,
        footer,
        nav,
        .back-button,
        .print-button,
        .period-filter {
            display: none !important;
        }

        /* Print-specific styles */
        body {
            background: white !important;
            color: black !important;
            font-size: 12px !important;
        }

        .print-title {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .print-page {
            width: 100% !important;
            margin: 0 !important;
            padding: 20px !important;
            box-shadow: none !important;
            border: none !important;
        }

        .grid {
            display: block !important;
        }

        .grid > div {
            margin-bottom: 15px !important;
            page-break-inside: avoid;
        }

        .chart-container {
            height: 300px !important;
        }

        /* Force backgrounds and colors for print */
        .bg-white {
            background: white !important;
        }

        .shadow {
            box-shadow: none !important;
            border: 1px solid #ccc !important;
        }

        .rounded-lg {
            border-radius: 0 !important;
        }

        /* Page breaks */
        .page-break {
            page-break-before: always;
        }
    }

    @page {
        size: A4;
        margin: 1cm;
    }
</style>
@endpush

@section('content')
    <!-- Print Title (Hidden on screen, visible on print) -->
    <div class="print-title" style="display: none;">
        <h1>LAPORAN ANALYTICS DASHBOARD</h1>
        <h2>RT 02/RW 04 Kedurus - Kepatihan</h2>
        <p>Tanggal: {{ now()->format('d F Y') }}</p>
    </div>

    <!-- Back to Homepage -->
    <div class="mb-4 back-button no-print">
        <a href="{{ url('/') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Beranda
        </a>
    </div>

    <div class="mb-6 flex justify-between items-center print-hide no-print">
        <div>
            <h1 class="text-2xl font-bold">Dashboard Analytics</h1>
            <p class="text-slate-600">Statistik dan analisis data RT secara mendalam</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition">
                ← Kembali ke Dashboard
            </a>
            <button onclick="printReport()" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition print-button">
                🖨️ Print Report
            </button>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow period-filter no-print">
        <div class="flex items-center justify-between">
            <h3 class="font-semibold">Filter Periode</h3>
            <div class="flex gap-2" x-data="{ period: 'current' }">
                <button @click="period = 'current'"
                        :class="period === 'current' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                        class="px-3 py-1 rounded text-sm hover:bg-blue-500 hover:text-white transition">
                    Bulan Ini
                </button>
                <button @click="period = 'last'"
                        :class="period === 'last' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                        class="px-3 py-1 rounded text-sm hover:bg-blue-500 hover:text-white transition">
                    Bulan Lalu
                </button>
                <button @click="period = 'quarter'"
                        :class="period === 'quarter' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                        class="px-3 py-1 rounded text-sm hover:bg-blue-500 hover:text-white transition">
                    3 Bulan
                </button>
                <button @click="period = 'year'"
                        :class="period === 'year' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                        class="px-3 py-1 rounded text-sm hover:bg-blue-500 hover:text-white transition">
                    Tahun Ini
                </button>
            </div>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Total Keluarga</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_households'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Total Warga</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['total_residents'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Iuran Bulan Ini</h3>
                    @php
                        $percentage = $duesStats['total_dues'] > 0 ? round(($duesStats['paid_dues'] / $duesStats['total_dues']) * 100) : 0;
                    @endphp
                    <p class="text-3xl font-bold text-yellow-600">{{ $percentage }}%</p>
                    <p class="text-sm text-slate-500">{{ $duesStats['paid_dues'] }}/{{ $duesStats['total_dues'] }} lunas</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Surat Pending</h3>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['pending_letters'] }}</p>
                    <p class="text-sm text-slate-500">Perlu diproses</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Performance Chart -->
    <div class="grid lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Performa Keuangan (6 Bulan Terakhir)</h3>
            <div class="h-64" x-data="financialChart">
                <canvas x-ref="chart" class="w-full h-full"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Detail Keuangan Bulan Ini</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-slate-50 rounded">
                    <span class="font-medium">Target Iuran</span>
                    <span class="text-green-600 font-bold">Rp {{ number_format($duesStats['total_amount'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-slate-50 rounded">
                    <span class="font-medium">Terkumpul</span>
                    <span class="text-blue-600 font-bold">Rp {{ number_format($duesStats['paid_amount'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-slate-50 rounded">
                    <span class="font-medium">Sisa Target</span>
                    <span class="text-red-600 font-bold">Rp {{ number_format($duesStats['total_amount'] - $duesStats['paid_amount'], 0, ',', '.') }}</span>
                </div>

                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-slate-600 mb-1">
                        <span>Progress</span>
                        <span>{{ $percentage }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full transition-all duration-300"
                             style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Letter Statistics & Demographics -->
    <div class="grid lg:grid-cols-3 gap-6 mb-8">
        <!-- Letter Stats -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Statistik Surat</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-slate-600">Total Pengajuan</span>
                    <span class="font-bold">{{ $letterStats['total'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-yellow-600">Pending</span>
                    <span class="font-bold text-yellow-600">{{ $letterStats['pending'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-green-600">Disetujui</span>
                    <span class="font-bold text-green-600">{{ $letterStats['approved'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-red-600">Ditolak</span>
                    <span class="font-bold text-red-600">{{ $letterStats['rejected'] }}</span>
                </div>
            </div>

            <!-- Letter Types -->
            <div class="mt-6 pt-4 border-t">
                <h4 class="font-medium mb-3">Jenis Surat Terpopuler</h4>
                @foreach($letterTypes as $type)
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm capitalize">{{ $type->type }}</span>
                        <span class="text-sm font-medium">{{ $type->count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Demographics -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Demografi Warga</h3>

            <!-- Gender Distribution -->
            <div class="mb-6">
                <h4 class="font-medium mb-3">Jenis Kelamin</h4>
                @foreach($demographics['gender'] as $gender)
                    @php
                        $total = $stats['total_residents'];
                        $percentage = $total > 0 ? round(($gender->count / $total) * 100) : 0;
                        $genderLabel = $gender->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
                        $colorClass = $gender->jenis_kelamin === 'L' ? 'bg-blue-500' : 'bg-pink-500';
                    @endphp
                    <div class="mb-2">
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ $genderLabel }}</span>
                            <span>{{ $gender->count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Age Groups -->
            <div>
                <h4 class="font-medium mb-3">Kelompok Usia</h4>
                @foreach($demographics['age_groups'] as $ageGroup => $count)
                    @php
                        $total = $stats['total_residents'];
                        $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
                    @endphp
                    <div class="mb-2">
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ $ageGroup }} tahun</span>
                            <span>{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Aktivitas Terbaru</h3>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @foreach($recentActivities as $activity)
                    <div class="flex items-start space-x-3 p-3 bg-slate-50 rounded">
                        <div class="text-xl">{{ $activity['icon'] }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-900">{{ $activity['message'] }}</p>
                            <div class="flex items-center space-x-2 mt-1">
                                <p class="text-xs text-slate-500">
                                    {{ $activity['date'] ? $activity['date']->diffForHumans() : 'Waktu tidak diketahui' }}
                                </p>
                                @if(isset($activity['amount']))
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                        Rp {{ number_format($activity['amount'], 0, ',', '.') }}
                                    </span>
                                @endif
                                @if(isset($activity['status']))
                                    <span class="text-xs px-2 py-1 rounded
                                        {{ $activity['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                           ($activity['status'] === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($activity['status']) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Export Functions -->
    <script>
        // Function to export data as CSV
        function exportToCSV() {
            const financialData = @json($financialData);
            let csv = 'Bulan,Target,Terkumpul,Persentase\n';

            financialData.forEach(row => {
                csv += `${row.month},${row.target},${row.collected},${row.percentage}%\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'financial_report_' + new Date().toISOString().slice(0, 10) + '.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        // Add export button functionality
        document.addEventListener('DOMContentLoaded', function() {
            const exportBtn = document.createElement('button');
            exportBtn.innerHTML = '📊 Export CSV';
            exportBtn.className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition ml-2';
            exportBtn.onclick = exportToCSV;

            // Find the header button group and append export button
            const buttonGroup = document.querySelector('.mb-6 .flex.gap-2');
            if (buttonGroup) {
                buttonGroup.appendChild(exportBtn);
            }
        });
    </script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('financialChart', () => ({
                init() {
                    const ctx = this.$refs.chart.getContext('2d');
                    const financialData = @json($financialData);

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: financialData.map(d => d.month_short),
                            datasets: [{
                                label: 'Target',
                                data: financialData.map(d => d.target),
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true
                            }, {
                                label: 'Terkumpul',
                                data: financialData.map(d => d.collected),
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            },
                            elements: {
                                point: {
                                    radius: 4,
                                    hoverRadius: 6
                                },
                                line: {
                                    tension: 0.4
                                }
                            }
                        }
                    });
                }
            }))
        })
    </script>

    <!-- Print Function -->
    <script>
        function printReport() {
            // Show print title
            document.querySelector('.print-title').style.display = 'block';

            // Add print-page class to main content
            document.querySelector('main').classList.add('print-page');

            // Prepare charts for printing
            const charts = document.querySelectorAll('canvas');
            charts.forEach(chart => {
                chart.style.maxHeight = '300px';
            });

            // Print the page
            window.print();

            // Clean up after printing
            setTimeout(() => {
                document.querySelector('.print-title').style.display = 'none';
                document.querySelector('main').classList.remove('print-page');
            }, 1000);
        }

        // Ensure charts are responsive
        window.addEventListener('beforeprint', function() {
            // Prepare for print
            Chart.helpers.each(Chart.instances, function(instance) {
                instance.resize();
            });
        });

        window.addEventListener('afterprint', function() {
            // Cleanup after print
            Chart.helpers.each(Chart.instances, function(instance) {
                instance.resize();
            });
        });
    </script>
@endsection
