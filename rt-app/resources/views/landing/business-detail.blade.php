@extends('layouts.landing')

@section('title', $business->name . ' - UMKM Warga')
@section('description', 'Detail informasi ' . $business->name . ' - ' . Str::limit($business->description, 150))

@section('content')
<!-- Header Section -->
<section class="hero-gradient py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center text-white">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">
                {{ $business->name }}
            </h1>
            <p class="text-lg opacity-90">
                {{ $business->owner_name }}
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
            <li>
                <a href="{{ route('landing.businesses') }}" class="text-gray-500 hover:text-gray-700">
                    UMKM
                </a>
            </li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-900 font-medium">{{ $business->name }}</li>
        </ol>
    </div>
</nav>

<!-- Main Content -->
<section class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Business Image -->
            <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                <img src="{{ $business->image_url }}"
                     alt="{{ $business->name }}"
                     class="w-full h-64 object-cover">
            </div>

            <!-- Business Details -->
            <div class="p-8">
                <!-- Header -->
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="flex items-center mb-2">
                            <span class="bg-orange-100 text-orange-800 text-sm font-semibold px-3 py-1 rounded-full mr-3">
                                {{ \App\Models\Business::getCategories()[$business->category] ?? $business->category }}
                            </span>
                            @if($business->featured)
                                <span class="bg-yellow-100 text-yellow-800 text-sm font-semibold px-3 py-1 rounded-full">
                                    <i class="fas fa-star mr-1"></i>Unggulan
                                </span>
                            @endif
                        </div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $business->name }}</h1>
                        <p class="text-lg text-gray-600 flex items-center">
                            <i class="fas fa-user mr-2"></i>{{ $business->owner_name }}
                        </p>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Deskripsi</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $business->description }}</p>
                </div>

                <!-- Contact Information -->
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <!-- Left Column -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Kontak</h3>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-orange-600 mt-1 mr-3"></i>
                                <div>
                                    <p class="font-semibold text-gray-700">Alamat</p>
                                    <p class="text-gray-600">{{ $business->address }}</p>
                                </div>
                            </div>

                            @if($business->phone)
                            <div class="flex items-center">
                                <i class="fas fa-phone text-orange-600 mr-3"></i>
                                <div>
                                    <p class="font-semibold text-gray-700">Telepon</p>
                                    <a href="tel:{{ $business->phone }}" class="text-green-600 hover:text-green-700">
                                        {{ $business->formatted_phone }}
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($business->email)
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-orange-600 mr-3"></i>
                                <div>
                                    <p class="font-semibold text-gray-700">Email</p>
                                    <a href="mailto:{{ $business->email }}" class="text-green-600 hover:text-green-700">
                                        {{ $business->email }}
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Media Sosial</h3>
                        <div class="space-y-3">
                            @if($business->whatsapp)
                            <div class="flex items-center">
                                <i class="fab fa-whatsapp text-green-600 text-lg mr-3"></i>
                                <div>
                                    <p class="font-semibold text-gray-700">WhatsApp</p>
                                    <a href="{{ $business->whatsapp_link }}"
                                       target="_blank"
                                       class="text-green-600 hover:text-green-700">
                                        {{ $business->whatsapp }}
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($business->instagram)
                            <div class="flex items-center">
                                <i class="fab fa-instagram text-pink-600 text-lg mr-3"></i>
                                <div>
                                    <p class="font-semibold text-gray-700">Instagram</p>
                                    <a href="https://instagram.com/{{ ltrim($business->instagram, '@') }}"
                                       target="_blank"
                                       class="text-pink-600 hover:text-pink-700">
                                        {{ $business->instagram }}
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($business->facebook)
                            <div class="flex items-center">
                                <i class="fab fa-facebook text-green-600 text-lg mr-3"></i>
                                <div>
                                    <p class="font-semibold text-gray-700">Facebook</p>
                                    <a href="https://facebook.com/{{ $business->facebook }}"
                                       target="_blank"
                                       class="text-green-600 hover:text-green-700">
                                        {{ $business->facebook }}
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($business->website)
                            <div class="flex items-center">
                                <i class="fas fa-globe text-gray-600 text-lg mr-3"></i>
                                <div>
                                    <p class="font-semibold text-gray-700">Website</p>
                                    <a href="{{ $business->website }}"
                                       target="_blank"
                                       class="text-green-600 hover:text-green-700">
                                        {{ $business->website }}
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Operating Hours -->
                @if($business->operating_hours)
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Jam Operasional</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach($business->operating_hours as $day => $hours)
                            <div class="flex justify-between items-center py-2">
                                <span class="font-medium text-gray-700 capitalize">{{ $day }}</span>
                                <span class="text-gray-600">{{ $hours ?: 'Tutup' }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Contact Actions -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Hubungi Sekarang</h3>
                    <div class="flex flex-wrap gap-4">
                        @if($business->whatsapp)
                        <a href="{{ $business->whatsapp_link }}"
                           target="_blank"
                           class="inline-flex items-center bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                            <i class="fab fa-whatsapp mr-2"></i>Chat WhatsApp
                        </a>
                        @endif

                        @if($business->phone)
                        <a href="tel:{{ $business->phone }}"
                           class="inline-flex items-center bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                            <i class="fas fa-phone mr-2"></i>Telepon
                        </a>
                        @endif

                        @if($business->email)
                        <a href="mailto:{{ $business->email }}"
                           class="inline-flex items-center bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-700 transition-colors">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to List -->
        <div class="mt-8 text-center">
            <a href="{{ route('landing.businesses') }}"
               class="inline-flex items-center text-orange-600 hover:text-orange-700 font-semibold">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar UMKM
            </a>
        </div>
    </div>
</section>

<!-- Related Businesses -->
@php
    $relatedBusinesses = \App\Models\Business::active()
        ->where('category', $business->category)
        ->where('id', '!=', $business->id)
        ->take(3)
        ->get();
@endphp

@if($relatedBusinesses->count() > 0)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">
            UMKM Sejenis Lainnya
        </h2>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($relatedBusinesses as $related)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
                <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                    <img src="{{ $related->image_url }}"
                         alt="{{ $related->name }}"
                         class="w-full h-48 object-cover">
                </div>

                <div class="p-6">
                    <span class="bg-orange-100 text-orange-800 text-xs font-semibold px-3 py-1 rounded-full">
                        {{ \App\Models\Business::getCategories()[$related->category] ?? $related->category }}
                    </span>

                    <h3 class="text-xl font-bold text-gray-800 mt-3 mb-2">{{ $related->name }}</h3>
                    <p class="text-gray-600 text-sm mb-3">{{ $related->owner_name }}</p>
                    <p class="text-gray-600 mb-4 line-clamp-2">{{ Str::limit($related->description, 80) }}</p>

                    <div class="flex items-center justify-between">
                        @if($related->whatsapp)
                            <a href="{{ $related->whatsapp_link }}"
                               target="_blank"
                               class="text-green-600 hover:text-green-700">
                                <i class="fab fa-whatsapp text-lg"></i>
                            </a>
                        @endif

                        <a href="{{ route('landing.business-detail', $related) }}"
                           class="text-orange-600 hover:text-orange-700 font-semibold text-sm">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
