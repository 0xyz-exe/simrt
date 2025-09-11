@extends('layouts.landing')
@section('title', $activity->title)
@section('content')
<!-- Hero Section -->
<section class="hero-gradient py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <nav class="flex justify-center mb-4">
                <ol class="flex items-center space-x-2 text-green-100">
                    <li><a href="{{ route('landing') }}" class="hover:text-white">Beranda</a></li>
                    <li>/</li>
                    <li><a href="{{ route('landing.activities') }}" class="hover:text-white">Kegiatan</a></li>
                    <li>/</li>
                    <li class="text-white">{{ $activity->title }}</li>
                </ol>
            </nav>

            <div class="flex justify-center flex-wrap gap-2 mb-4">
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $activity->status_badge }}">
                    {{ $activity->status_text }}
                </span>
                <span class="px-3 py-1 bg-white bg-opacity-20 text-white rounded-full text-sm font-medium">
                    {{ $activity->type_text }}
                </span>
                @if($activity->is_featured)
                    <span class="px-3 py-1 bg-yellow-400 text-yellow-900 rounded-full text-sm font-bold">
                        <i class="fas fa-star mr-1"></i>Unggulan
                    </span>
                @endif
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">
                {{ $activity->title }}
            </h1>
        </div>
    </div>
</section>

<!-- Activity Details -->
<section class="py-12 sm:py-16 lg:py-20 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Activity Image -->
                @if($activity->image)
                    <div class="mb-8">
                        <img src="{{ Storage::url($activity->image) }}"
                             alt="{{ $activity->title }}"
                             class="w-full h-64 sm:h-80 lg:h-96 object-cover rounded-xl shadow-lg">
                    </div>
                @endif

                <!-- Description -->
                <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Deskripsi Kegiatan</h2>
                    <div class="prose prose-lg max-w-none text-gray-600">
                        {!! nl2br(e($activity->description)) !!}
                    </div>
                </div>

                <!-- Requirements -->
                @if($activity->requirements)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-xl p-6 sm:p-8 mb-8">
                        <h3 class="text-xl font-bold text-yellow-800 mb-4">Yang Perlu Dibawa</h3>
                        <div class="text-yellow-700">
                            {!! nl2br(e($activity->requirements)) !!}
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4">
                    @if($activity->contact_phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $activity->contact_phone) }}"
                           target="_blank"
                           class="inline-flex items-center bg-green-600 text-white px-8 py-4 rounded-lg hover:bg-green-700 transition-colors text-lg font-semibold">
                            <i class="fab fa-whatsapp mr-3"></i>Hubungi Koordinator
                        </a>
                    @endif

                    <a href="{{ route('landing.activities') }}"
                       class="inline-flex items-center border-2 border-blue-600 text-blue-600 px-8 py-4 rounded-lg hover:bg-blue-50 transition-colors text-lg font-semibold">
                        <i class="fas fa-calendar-alt mr-3"></i>Lihat Kegiatan Lain
                    </a>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="lg:col-span-1">
                <!-- Quick Info -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Informasi Kegiatan</h3>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-calendar-day w-5 mt-1 mr-3 text-blue-600"></i>
                            <div>
                                <p class="font-semibold text-gray-700">Tanggal</p>
                                <p class="text-gray-600">{{ $activity->formatted_date }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <i class="fas fa-clock w-5 mt-1 mr-3 text-green-600"></i>
                            <div>
                                <p class="font-semibold text-gray-700">Waktu</p>
                                <p class="text-gray-600">{{ $activity->formatted_time }}</p>
                            </div>
                        </div>

                        @if($activity->location)
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt w-5 mt-1 mr-3 text-red-600"></i>
                                <div>
                                    <p class="font-semibold text-gray-700">Lokasi</p>
                                    <p class="text-gray-600">{{ $activity->location }}</p>
                                </div>
                            </div>
                        @endif

                        @if($activity->max_participants)
                            <div class="flex items-start">
                                <i class="fas fa-users w-5 mt-1 mr-3 text-purple-600"></i>
                                <div>
                                    <p class="font-semibold text-gray-700">Peserta</p>
                                    <p class="text-gray-600">
                                        {{ $activity->current_participants }}/{{ $activity->max_participants }} orang
                                        @if($activity->participation_status)
                                            <span class="block text-sm text-gray-500">({{ $activity->participation_status }})</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Contact Info -->
                @if($activity->contact_person || $activity->contact_phone)
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Koordinator</h3>

                        <div class="space-y-3">
                            @if($activity->contact_person)
                                <div class="flex items-center">
                                    <i class="fas fa-user w-5 mr-3 text-orange-600"></i>
                                    <span class="text-gray-600">{{ $activity->contact_person }}</span>
                                </div>
                            @endif

                            @if($activity->contact_phone)
                                <div class="flex items-center">
                                    <i class="fas fa-phone w-5 mr-3 text-green-600"></i>
                                    <span class="text-gray-600">{{ $activity->contact_phone }}</span>
                                </div>
                            @endif
                        </div>

                        @if($activity->contact_phone)
                            <div class="mt-4">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $activity->contact_phone) }}"
                                   target="_blank"
                                   class="w-full inline-flex items-center justify-center bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fab fa-whatsapp mr-2"></i>Chat WhatsApp
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Share -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Bagikan Kegiatan</h3>

                    <div class="grid grid-cols-2 gap-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                           target="_blank"
                           class="flex items-center justify-center bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fab fa-facebook-f mr-2"></i>Facebook
                        </a>

                        <a href="https://wa.me/?text={{ urlencode($activity->title . ' - ' . request()->fullUrl()) }}"
                           target="_blank"
                           class="flex items-center justify-center bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fab fa-whatsapp mr-2"></i>WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Activities -->
@php
    $relatedActivities = App\Models\Activity::active()
        ->upcoming()
        ->where('id', '!=', $activity->id)
        ->where('type', $activity->type)
        ->orderBy('start_date', 'asc')
        ->take(3)
        ->get();
@endphp

@if($relatedActivities->count() > 0)
    <section class="py-12 sm:py-16 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-12">Kegiatan Serupa</h2>

            <div class="grid md:grid-cols-3 gap-8">
                @foreach($relatedActivities as $related)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
                        <div class="h-48 relative overflow-hidden">
                            @if($related->image)
                                <img src="{{ Storage::url($related->image) }}"
                                     alt="{{ $related->title }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-white text-3xl"></i>
                                </div>
                            @endif
                        </div>

                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2">{{ $related->title }}</h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $related->description }}</p>

                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <i class="fas fa-calendar-day mr-2"></i>
                                <span>{{ $related->formatted_date }}</span>
                            </div>

                            <a href="{{ route('landing.activity-detail', $related) }}"
                               class="text-blue-600 hover:text-blue-700 font-semibold">
                                Lihat Detail →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

<!-- CTA Section -->
<section class="py-16 hero-gradient">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Ikuti Kegiatan RT Lainnya
        </h2>
        <p class="text-xl opacity-90 mb-8">
            Jangan lewatkan kegiatan menarik lainnya untuk mempererat silaturahmi warga RT
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('landing.activities') }}"
               class="bg-white text-rt-green px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                <i class="fas fa-calendar-alt mr-2"></i>Lihat Semua Kegiatan
            </a>
            <a href="{{ route('landing') }}"
               class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-rt-green transition-colors">
                <i class="fas fa-home mr-2"></i>Kembali ke Beranda
            </a>
        </div>
    </div>
</section>
@endsection
