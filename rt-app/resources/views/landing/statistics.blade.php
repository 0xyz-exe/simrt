@extends('layouts.landing')

@section('title', 'Statistik Warga - Guyub Rukun Warga RT 02/RW 04 Kedurus - Kepatihan Kedurus Kepatihan')
@section('description', 'Data demografi dan statistik lengkap warga RT 02/RW 04 Kedurus - Kepatihan Kedurus Kepatihan termasuk usia, pekerjaan, pendidikan, dan informasi lainnya')

@section('extra_css')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .stat-card {
        background: linear-gradient(135deg, var(--rt-green) 0%, var(--rt-orange) 100%);
    }

    .stat-card-green {
        background: linear-gradient(135deg, var(--rt-green) 0%, var(--rt-light-green) 100%);
    }

    .stat-card-orange {
        background: linear-gradient(135deg, var(--rt-orange) 0%, #e07600 100%);
    }

    .stat-card-purple {
        background: linear-gradient(135deg, var(--rt-dark-green) 0%, var(--rt-green) 100%);
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    @media (max-width: 640px) {
        .chart-container {
            height: 250px;
        }
    }
</style>
@endsection

@section('content')
<!-- Header Section -->
<section class="hero-gradient text-white py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-4 sm:mb-6">
                📊 Statistik Warga
            </h1>
            <p class="text-lg sm:text-xl lg:text-2xl mb-6 sm:mb-8 max-w-3xl mx-auto">
                Data demografi dan statistik lengkap warga RT
            </p>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                <a href="#overview" class="bg-white text-green-600 px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    <i class="fas fa-chart-bar mr-2"></i>Lihat Statistik
                </a>
                <a href="{{ route('landing') }}" class="border-2 border-white text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:bg-white hover:text-green-600 transition-colors">
                    <i class="fas fa-home mr-2"></i>Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Overview Statistics -->
<section id="overview" class="py-12 sm:py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 sm:mb-12">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 mb-3 sm:mb-4">Ringkasan Demografi</h2>
            <p class="text-base sm:text-lg text-gray-600">Data terkini populasi dan keluarga di RT</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
            <!-- Total Penduduk -->
            <div class="stat-card rounded-xl p-4 sm:p-6 text-white text-center card-hover">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <i class="fas fa-users text-lg sm:text-2xl"></i>
                </div>
                <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-1 sm:mb-2">{{ $demographicData['total_residents'] }}</h3>
                <p class="text-sm sm:text-base opacity-90">Total Penduduk</p>
            </div>

            <!-- Total Keluarga -->
            <div class="stat-card-green rounded-xl p-4 sm:p-6 text-white text-center card-hover">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <i class="fas fa-home text-lg sm:text-2xl"></i>
                </div>
                <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-1 sm:mb-2">{{ $demographicData['total_families'] }}</h3>
                <p class="text-sm sm:text-base opacity-90">Keluarga</p>
            </div>

            <!-- Rata-rata Anggota Keluarga -->
            <div class="stat-card-orange rounded-xl p-4 sm:p-6 text-white text-center card-hover">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <i class="fas fa-user-friends text-lg sm:text-2xl"></i>
                </div>
                <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-1 sm:mb-2">{{ $statistics['avg_family_size'] }}</h3>
                <p class="text-sm sm:text-base opacity-90">Rata-rata/Keluarga</p>
            </div>

            <!-- Kepadatan Penduduk -->
            <div class="stat-card-purple rounded-xl p-4 sm:p-6 text-white text-center card-hover">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <i class="fas fa-map text-lg sm:text-2xl"></i>
                </div>
                <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-1 sm:mb-2">{{ $demographicData['population_density'] }}</h3>
                <p class="text-sm sm:text-base opacity-90">Orang/Hektar</p>
            </div>
        </div>
    </div>
</section>

<!-- Age Demographics -->
<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Age Distribution Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">
                    <i class="fas fa-birthday-cake text-green-600 mr-3"></i>Distribusi Usia
                </h3>
                <div class="chart-container">
                    <canvas id="ageChart"></canvas>
                </div>
            </div>

            <!-- Age Statistics -->
            <div class="space-y-4 sm:space-y-6">
                <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">
                        <i class="fas fa-chart-line text-green-600 mr-3"></i>Analisis Usia
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 sm:p-4 bg-green-50 rounded-lg">
                            <span class="font-semibold text-gray-700 text-sm sm:text-base">Anak-anak (0-17 tahun)</span>
                            <span class="text-lg sm:text-xl font-bold text-green-600">{{ $statistics['children_percentage'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 sm:p-4 bg-green-50 rounded-lg">
                            <span class="font-semibold text-gray-700 text-sm sm:text-base">Usia Produktif (18-60 tahun)</span>
                            <span class="text-lg sm:text-xl font-bold text-green-600">{{ $statistics['working_age_percentage'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 sm:p-4 bg-orange-50 rounded-lg">
                            <span class="font-semibold text-gray-700 text-sm sm:text-base">Lansia (60+ tahun)</span>
                            <span class="text-lg sm:text-xl font-bold text-orange-600">{{ $statistics['elderly_percentage'] }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Gender Distribution -->
                <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">
                        <i class="fas fa-venus-mars text-orange-600 mr-3"></i>Jenis Kelamin
                    </h3>
                    <div class="space-y-3 sm:space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 text-sm sm:text-base">👨 Laki-laki</span>
                            <span class="font-bold text-green-600 text-sm sm:text-base">{{ $demographicData['gender']['male'] }} orang</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 text-sm sm:text-base">👩 Perempuan</span>
                            <span class="font-bold text-pink-600 text-sm sm:text-base">{{ $demographicData['gender']['female'] }} orang</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Occupation & Education -->
<section class="py-12 sm:py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Occupation Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">
                    <i class="fas fa-briefcase text-green-600 mr-3"></i>Pekerjaan
                </h3>
                <div class="chart-container">
                    <canvas id="occupationChart"></canvas>
                </div>
                <div class="mt-4 sm:mt-6">
                    <div class="text-center p-3 sm:p-4 bg-green-50 rounded-lg">
                        <span class="text-sm sm:text-base text-gray-600">Tingkat Kesempatan Kerja</span>
                        <div class="text-xl sm:text-2xl font-bold text-green-600">{{ $statistics['employment_rate'] }}%</div>
                    </div>
                </div>
            </div>

            <!-- Education Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">
                    <i class="fas fa-graduation-cap text-orange-600 mr-3"></i>Pendidikan
                </h3>
                <div class="chart-container">
                    <canvas id="educationChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Other Demographics -->
<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 sm:mb-12">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 mb-3 sm:mb-4">Data Demografis Lainnya</h2>
            <p class="text-base sm:text-lg text-gray-600">Informasi tambahan tentang warga RT</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Marital Status -->
            <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 sm:mb-6">
                    <i class="fas fa-heart text-red-600 mr-3"></i>Status Pernikahan
                </h3>
                <div class="space-y-3">
                    @foreach($demographicData['marital_status'] as $status => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 text-sm sm:text-base">{{ $status }}</span>
                        <span class="font-bold text-gray-800 text-sm sm:text-base">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- House Ownership -->
            <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 sm:mb-6">
                    <i class="fas fa-home text-green-600 mr-3"></i>Kepemilikan Rumah
                </h3>
                <div class="space-y-3">
                    @foreach($demographicData['house_ownership'] as $type => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 text-sm sm:text-base">{{ $type }}</span>
                        <span class="font-bold text-gray-800 text-sm sm:text-base">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Vehicle Ownership -->
            <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 sm:mb-6">
                    <i class="fas fa-car text-green-600 mr-3"></i>Kepemilikan Kendaraan
                </h3>
                <div class="space-y-3">
                    @foreach($demographicData['vehicles'] as $vehicle => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 text-sm sm:text-base">{{ $vehicle }}</span>
                        <span class="font-bold text-gray-800 text-sm sm:text-base">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Real-time Updates -->
<section class="py-12 sm:py-16 hero-gradient text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-4 sm:mb-6">
            📈 Data Real-time
        </h2>
        <p class="text-base sm:text-lg lg:text-xl mb-6 sm:mb-8">
            Statistik ini diperbarui secara otomatis setiap ada perubahan data kependudukan
        </p>
        <div class="bg-white bg-opacity-20 rounded-lg p-4 sm:p-6 inline-block">
            <div class="text-sm sm:text-base opacity-90 mb-2">Terakhir diperbarui</div>
            <div id="lastUpdate" class="text-lg sm:text-xl font-bold">{{ now()->format('d M Y, H:i') }} WIB</div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
// Age Distribution Chart
const ageCtx = document.getElementById('ageChart').getContext('2d');
const ageChart = new Chart(ageCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($demographicData['age_groups'])) !!},
        datasets: [{
            data: {!! json_encode(array_values($demographicData['age_groups'])) !!},
            backgroundColor: [
                '#ff6384',
                '#36a2eb',
                '#ffce56',
                '#4bc0c0',
                '#9966ff',
                '#ff9f40'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    font: {
                        size: window.innerWidth < 640 ? 10 : 12
                    }
                }
            }
        }
    }
});

// Occupation Chart
const occupationCtx = document.getElementById('occupationChart').getContext('2d');
const occupationChart = new Chart(occupationCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($demographicData['occupations'])) !!},
        datasets: [{
            data: {!! json_encode(array_values($demographicData['occupations'])) !!},
            backgroundColor: 'rgba(72, 187, 120, 0.8)',
            borderColor: 'rgba(72, 187, 120, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 5
                }
            },
            x: {
                ticks: {
                    maxRotation: 45,
                    font: {
                        size: window.innerWidth < 640 ? 10 : 12
                    }
                }
            }
        }
    }
});

// Education Chart
const educationCtx = document.getElementById('educationChart').getContext('2d');
const educationChart = new Chart(educationCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode(array_keys($demographicData['education'])) !!},
        datasets: [{
            data: {!! json_encode(array_values($demographicData['education'])) !!},
            backgroundColor: [
                '#667eea',
                '#764ba2',
                '#f093fb',
                '#f5576c',
                '#4facfe',
                '#00f2fe'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    font: {
                        size: window.innerWidth < 640 ? 10 : 12
                    }
                }
            }
        }
    }
});

// Real-time update simulation
function updateTimestamp() {
    const now = new Date();
    const options = {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    };
    document.getElementById('lastUpdate').textContent =
        now.toLocaleDateString('id-ID', options) + ' WIB';
}

// Update every 30 seconds
setInterval(updateTimestamp, 30000);

// Responsive chart handling
function handleResize() {
    ageChart.resize();
    occupationChart.resize();
    educationChart.resize();
}

window.addEventListener('resize', handleResize);
</script>
@endsection
