@extends('layouts.landing')
@section('title', 'Kegiatan RT')
@section('content')
<!-- Hero Section -->
<section class="hero-gradient py-16 sm:py-20 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="animate-on-scroll">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4 sm:mb-6">
                🗓️ Kegiatan RT 02/RW 04
            </h1>
            <p class="text-lg sm:text-xl lg:text-2xl text-green-100 max-w-3xl mx-auto">
                Jadwal kegiatan dan acara untuk mempererat silaturahmi warga RT
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
            <li class="text-gray-900 font-medium">Kegiatan</li>
        </ol>
    </div>
</nav>

<!-- Filter Section -->
<section class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Filter Kegiatan</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <select id="statusFilter" class="border rounded-lg px-3 py-2">
                    <option value="">Semua Status</option>
                    <option value="scheduled">Dijadwalkan</option>
                    <option value="ongoing">Sedang Berlangsung</option>
                    <option value="completed">Selesai</option>
                </select>

                <select id="typeFilter" class="border rounded-lg px-3 py-2">
                    <option value="">Semua Jenis</option>
                    <option value="gotong_royong">Gotong Royong</option>
                    <option value="rapat">Rapat RT</option>
                    <option value="acara_khusus">Acara Khusus</option>
                    <option value="olahraga">Olahraga</option>
                    <option value="sosial">Sosial</option>
                    <option value="keagamaan">Keagamaan</option>
                    <option value="pendidikan">Pendidikan</option>
                    <option value="kesehatan">Kesehatan</option>
                </select>

                <input type="month" id="monthFilter" class="border rounded-lg px-3 py-2">

                <button onclick="resetFilters()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Activities List -->
<section class="py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($activities->count() > 0)
            <div class="grid gap-8">
                @foreach($activities as $activity)
                    <div class="activity-card bg-white rounded-xl shadow-lg overflow-hidden card-hover animate-on-scroll"
                         data-status="{{ $activity->status }}"
                         data-type="{{ $activity->type }}"
                         data-date="{{ $activity->start_date->format('Y-m') }}">
                        <div class="md:flex">
                            <div class="md:w-80 h-64 md:h-auto">
                                @if($activity->image)
                                    <img src="{{ Storage::url($activity->image) }}"
                                         alt="{{ $activity->title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                        <i class="fas fa-calendar-alt text-white text-6xl"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 p-6 md:p-8">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $activity->status_badge }}">
                                                {{ $activity->status_text }}
                                            </span>
                                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                                {{ $activity->type_text }}
                                            </span>
                                            @if($activity->is_featured)
                                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                                    <i class="fas fa-star mr-1"></i>Unggulan
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-800 mb-3">{{ $activity->title }}</h3>
                                    </div>
                                </div>

                                <p class="text-gray-600 mb-6 text-lg leading-relaxed">{{ $activity->description }}</p>

                                <div class="grid md:grid-cols-2 gap-6 mb-6">
                                    <div class="space-y-3">
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-calendar-day w-5 mr-3 text-blue-600"></i>
                                            <span>{{ $activity->formatted_date }}</span>
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-clock w-5 mr-3 text-green-600"></i>
                                            <span>{{ $activity->formatted_time }}</span>
                                        </div>
                                        @if($activity->location)
                                            <div class="flex items-center text-gray-600">
                                                <i class="fas fa-map-marker-alt w-5 mr-3 text-red-600"></i>
                                                <span>{{ $activity->location }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="space-y-3">
                                        @if($activity->max_participants)
                                            <div class="flex items-center text-gray-600">
                                                <i class="fas fa-users w-5 mr-3 text-purple-600"></i>
                                                <span>{{ $activity->current_participants }}/{{ $activity->max_participants }} peserta</span>
                                                @if($activity->participation_status)
                                                    <span class="ml-2 text-sm text-gray-500">({{ $activity->participation_status }})</span>
                                                @endif
                                            </div>
                                        @endif
                                        @if($activity->contact_person)
                                            <div class="flex items-center text-gray-600">
                                                <i class="fas fa-user w-5 mr-3 text-orange-600"></i>
                                                <span>{{ $activity->contact_person }}</span>
                                            </div>
                                        @endif
                                        @if($activity->contact_phone)
                                            <div class="flex items-center text-gray-600">
                                                <i class="fas fa-phone w-5 mr-3 text-green-600"></i>
                                                <span>{{ $activity->contact_phone }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($activity->requirements)
                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                                        <h4 class="font-semibold text-yellow-800 mb-2">Yang Perlu Dibawa:</h4>
                                        <p class="text-yellow-700">{{ $activity->requirements }}</p>
                                    </div>
                                @endif

                                <div class="flex flex-wrap gap-3">
                                    @if($activity->contact_phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $activity->contact_phone) }}"
                                           target="_blank"
                                           class="inline-flex items-center bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                                            <i class="fab fa-whatsapp mr-2"></i>Hubungi Koordinator
                                        </a>
                                    @endif
                                    <a href="{{ route('landing.activity-detail', $activity) }}"
                                       class="inline-flex items-center border border-blue-600 text-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 transition-colors">
                                        <i class="fas fa-info-circle mr-2"></i>Detail Lengkap
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <i class="fas fa-calendar-times text-6xl text-gray-300 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-600 mb-4">Belum Ada Kegiatan</h3>
                <p class="text-gray-500 text-lg mb-8">Kegiatan mendatang akan ditampilkan di sini</p>
                <a href="{{ route('landing') }}"
                   class="inline-flex items-center bg-blue-600 text-white px-8 py-4 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-home mr-2"></i>Kembali ke Beranda
                </a>
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 hero-gradient">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Ikuti Kegiatan RT
        </h2>
        <p class="text-xl opacity-90 mb-8">
            Mari berpartisipasi dalam berbagai kegiatan untuk mempererat silaturahmi dan memajukan RT bersama-sama
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('landing') }}"
               class="bg-white text-rt-green px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                <i class="fas fa-home mr-2"></i>Kembali ke Beranda
            </a>
            @auth
                <a href="{{ route('dashboard') }}"
                   class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-rt-green transition-colors">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-rt-green transition-colors">
                    <i class="fas fa-sign-in-alt mr-2"></i>Masuk Portal
                </a>
            @endauth
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusFilter = document.getElementById('statusFilter');
        const typeFilter = document.getElementById('typeFilter');
        const monthFilter = document.getElementById('monthFilter');
        const activityCards = document.querySelectorAll('.activity-card');

        function filterActivities() {
            const selectedStatus = statusFilter.value;
            const selectedType = typeFilter.value;
            const selectedMonth = monthFilter.value;

            activityCards.forEach(card => {
                const cardStatus = card.dataset.status;
                const cardType = card.dataset.type;
                const cardDate = card.dataset.date;

                const statusMatch = !selectedStatus || cardStatus === selectedStatus;
                const typeMatch = !selectedType || cardType === selectedType;
                const monthMatch = !selectedMonth || cardDate === selectedMonth;

                if (statusMatch && typeMatch && monthMatch) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        statusFilter.addEventListener('change', filterActivities);
        typeFilter.addEventListener('change', filterActivities);
        monthFilter.addEventListener('change', filterActivities);
    });

    function resetFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('typeFilter').value = '';
        document.getElementById('monthFilter').value = '';

        document.querySelectorAll('.activity-card').forEach(card => {
            card.style.display = 'block';
        });
    }
</script>
@endpush
