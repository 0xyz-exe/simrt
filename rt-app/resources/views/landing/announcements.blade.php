@extends('layouts.landing')

@section('title', 'Pengumuman - Guyub Rukun Warga RT 02/RW 04 Kedurus - Kepatihan Kedurus Kepatihan')
@section('description', 'Daftar pengumuman dan informasi penting untuk warga RT 02/RW 04 Kedurus - Kepatihan Kedurus Kepatihan')

@section('content')
<!-- Header Section -->
<section class="hero-gradient py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                <i class="fas fa-bullhorn mr-3"></i>Pengumuman
            </h1>
            <p class="text-xl opacity-90 max-w-2xl mx-auto">
                Informasi penting dan terbaru untuk seluruh warga RT 02/RW 04
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
            <li class="text-gray-900 font-medium">Pengumuman</li>
        </ol>
    </div>
</nav>

<!-- Main Content -->
<section class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($announcements->count() > 0)
            <!-- Announcements List -->
            <div class="space-y-8">
                @foreach($announcements as $announcement)
                <article class="bg-white rounded-xl shadow-lg overflow-hidden card-hover animate-fade-in">
                    <div class="p-8">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rt-gradient rounded-full flex items-center justify-center">
                                    <i class="fas fa-bullhorn text-white"></i>
                                </div>
                                <div>
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                        Pengumuman
                                    </span>
                                    <div class="flex items-center text-sm text-gray-500 mt-2">
                                        <i class="fas fa-user mr-2"></i>{{ $announcement->user->name }}
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-calendar mr-2"></i>{{ $announcement->created_at->format('d F Y') }}
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-clock mr-2"></i>{{ $announcement->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                    <i class="fas fa-clock mr-1"></i>{{ $announcement->created_at->format('H:i') }}
                                </span>
                            </div>
                        </div>

                        <!-- Title -->
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">
                            {{ $announcement->title }}
                        </h2>

                        <!-- Content -->
                        <div class="prose prose-lg max-w-none text-gray-700">
                            {!! nl2br(e($announcement->content)) !!}
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($announcements->hasPages())
            <div class="mt-12">
                <nav class="flex justify-center">
                    <div class="flex items-center space-x-2">
                        {{-- Previous Page Link --}}
                        @if ($announcements->onFirstPage())
                            <span class="px-4 py-2 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                <i class="fas fa-chevron-left mr-1"></i>Sebelumnya
                            </span>
                        @else
                            <a href="{{ $announcements->previousPageUrl() }}" class="px-4 py-2 text-green-600 bg-white border border-gray-300 rounded-lg hover:bg-green-50 transition-colors">
                                <i class="fas fa-chevron-left mr-1"></i>Sebelumnya
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($announcements->getUrlRange(1, $announcements->lastPage()) as $page => $url)
                            @if ($page == $announcements->currentPage())
                                <span class="px-4 py-2 text-white bg-green-600 rounded-lg font-semibold">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($announcements->hasMorePages())
                            <a href="{{ $announcements->nextPageUrl() }}" class="px-4 py-2 text-green-600 bg-white border border-gray-300 rounded-lg hover:bg-green-50 transition-colors">
                                Selanjutnya<i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        @else
                            <span class="px-4 py-2 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                Selanjutnya<i class="fas fa-chevron-right ml-1"></i>
                            </span>
                        @endif
                    </div>
                </nav>

                <div class="text-center mt-4 text-sm text-gray-600">
                    Menampilkan {{ $announcements->firstItem() }}-{{ $announcements->lastItem() }} dari {{ $announcements->total() }} pengumuman
                </div>
            </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-bullhorn text-gray-400 text-5xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Belum Ada Pengumuman</h2>
                <p class="text-xl text-gray-600 mb-8 max-w-lg mx-auto">
                    Pengumuman dan informasi penting akan ditampilkan di halaman ini. Silakan kembali lagi nanti.
                </p>
                <a href="{{ route('landing') }}" class="inline-flex items-center bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                    <i class="fas fa-home mr-2"></i>Kembali ke Beranda
                </a>
            </div>
        @endif
    </div>
</section>

<!-- Back to Top Button -->
<button id="back-to-top" class="fixed bottom-6 right-6 bg-green-600 text-white p-3 rounded-full shadow-lg hover:bg-green-700 transition-all duration-300 opacity-0 invisible">
    <i class="fas fa-chevron-up"></i>
</button>
@endsection

@section('extra_js')
<script>
// Back to top button functionality
const backToTopButton = document.getElementById('back-to-top');

window.addEventListener('scroll', function() {
    if (window.pageYOffset > 300) {
        backToTopButton.classList.remove('opacity-0', 'invisible');
        backToTopButton.classList.add('opacity-100', 'visible');
    } else {
        backToTopButton.classList.remove('opacity-100', 'visible');
        backToTopButton.classList.add('opacity-0', 'invisible');
    }
});

backToTopButton.addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Animate announcements on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animationDelay = `${Array.from(entry.target.parentNode.children).indexOf(entry.target) * 0.1}s`;
            entry.target.classList.add('animate-fade-in');
        }
    });
}, observerOptions);

document.querySelectorAll('article').forEach(article => {
    observer.observe(article);
});
</script>
@endsection
