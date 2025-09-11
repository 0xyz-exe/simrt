<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', 'Sistem Informasi RT - Portal Resmi Rukun Tetangga')">
    <meta name="keywords" content="RT, RW, pengumuman, iuran, umkm, transparansi">
    <title>@yield('title', 'Guyub Rukun Warga - RT 02/RW 04 Kedurus - Kepatihan Kedurus Kepatihan')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-rt.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <style>
        :root {
            --rt-green: #4a7c59;
            --rt-orange: #ff8c00;
            --rt-light-green: #7fb069;
            --rt-dark-green: #2d5016;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .hero-gradient {
            background: linear-gradient(135deg, var(--rt-green) 0%, var(--rt-orange) 50%, var(--rt-light-green) 100%);
        }

        .rt-gradient {
            background: linear-gradient(135deg, var(--rt-green) 0%, var(--rt-orange) 100%);
        }

        .text-rt-green {
            color: var(--rt-green) !important;
        }

        .bg-rt-green {
            background-color: var(--rt-green) !important;
        }

        .border-rt-green {
            border-color: var(--rt-green) !important;
        }

        .hover\:text-rt-green:hover {
            color: var(--rt-green) !important;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(74, 124, 89, 0.15), 0 10px 10px -5px rgba(74, 124, 89, 0.08);
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .scrollspy-active {
            color: var(--rt-green) !important;
            border-color: var(--rt-green) !important;
        }

        .rt-logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        /* Placeholder untuk logo yang hilang */
        .logo-placeholder {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--rt-green) 0%, var(--rt-orange) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
    </style>

    @yield('extra_css')
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                        <img src="{{ asset('images/logo-rt.png') }}" alt="Logo RT 02/RW 04 Kedurus - Kepatihan Kedurus Kepatihan" class="rt-logo">
                        <div class="flex flex-col">
                            <span class="text-lg font-bold text-gray-800">Guyub Rukun Warga</span>
                            <span class="text-xs text-gray-600">RT 02/RW 04 Kedurus - Kepatihan </span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('landing') }}" class="nav-link {{ request()->routeIs('landing') ? 'text-rt-green border-b-2 border-rt-green' : 'text-gray-700 hover:text-rt-green' }} px-3 py-2 text-sm font-medium transition-colors">
                        Beranda
                    </a>
                    <a href="{{ route('landing.businesses') }}" class="nav-link {{ request()->routeIs('landing.businesses*') ? 'text-rt-green border-b-2 border-rt-green' : 'text-gray-700 hover:text-rt-green' }} px-3 py-2 text-sm font-medium transition-colors">
                        UMKM
                    </a>
                    <a href="{{ route('landing.transparency') }}" class="nav-link {{ request()->routeIs('landing.transparency') ? 'text-rt-green border-b-2 border-rt-green' : 'text-gray-700 hover:text-rt-green' }} px-3 py-2 text-sm font-medium transition-colors">
                        Transparansi
                    </a>
                    <a href="{{ route('login') }}" class="rt-gradient text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-500 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500 p-2">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="md:hidden hidden bg-white border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('landing') }}" class="block px-3 py-2 text-base font-medium {{ request()->routeIs('landing') ? 'text-rt-green bg-green-50' : 'text-gray-700 hover:text-rt-green hover:bg-gray-50' }} rounded-md">
                    <i class="fas fa-home mr-2"></i>Beranda
                </a>
                <a href="{{ route('landing.businesses') }}" class="block px-3 py-2 text-base font-medium {{ request()->routeIs('landing.businesses*') ? 'text-rt-green bg-green-50' : 'text-gray-700 hover:text-rt-green hover:bg-gray-50' }} rounded-md">
                    <i class="fas fa-store mr-2"></i>UMKM
                </a>
                <a href="{{ route('landing.transparency') }}" class="block px-3 py-2 text-base font-medium {{ request()->routeIs('landing.transparency') ? 'text-rt-green bg-green-50' : 'text-gray-700 hover:text-rt-green hover:bg-gray-50' }} rounded-md">
                    <i class="fas fa-chart-pie mr-2"></i>Transparansi
                </a>
                <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-white rt-gradient hover:opacity-90 rounded-md">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 lg:py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                <!-- Logo & Description -->
                <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                    <div class="flex items-center space-x-3 mb-3 sm:mb-4">
                        <img src="{{ asset('images/logo-rt.png') }}" alt="Logo RT" class="w-12 h-12 sm:w-16 sm:h-16">
                        <div class="flex flex-col">
                            <span class="text-xl sm:text-2xl font-bold">Guyub Rukun Warga</span>
                            <span class="text-sm text-gray-300">RT 02/RW 04 Kedurus - Kepatihan </span>
                        </div>
                    </div>
                    <p class="text-gray-300 mb-3 sm:mb-4 text-sm sm:text-base">
                        Membangun persatuan dan kebersamaan warga untuk menciptakan lingkungan yang nyaman, aman, dan sejahtera bagi seluruh keluarga.
                    </p>
                    <div class="flex space-x-3 sm:space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook text-lg sm:text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter text-lg sm:text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-lg sm:text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-whatsapp text-lg sm:text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Menu Utama</h3>
                    <ul class="space-y-1 sm:space-y-2">
                        <li><a href="{{ route('landing') }}" class="text-gray-300 hover:text-white transition-colors text-sm sm:text-base">Beranda</a></li>
                        <li><a href="{{ route('landing.announcements') }}" class="text-gray-300 hover:text-white transition-colors text-sm sm:text-base">Pengumuman</a></li>
                        <li><a href="{{ route('landing.businesses') }}" class="text-gray-300 hover:text-white transition-colors text-sm sm:text-base">UMKM</a></li>
                        <li><a href="{{ route('landing.statistics') }}" class="text-gray-300 hover:text-white transition-colors text-sm sm:text-base">Statistik</a></li>
                        <li><a href="{{ route('landing.transparency') }}" class="text-gray-300 hover:text-white transition-colors text-sm sm:text-base">Transparansi</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Kontak</h3>
                    <ul class="space-y-1 sm:space-y-2 text-gray-300">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mr-2 mt-1 text-xs sm:text-sm"></i>
                            <span class="text-xs sm:text-sm">Jl. Melati No. 123, Jakarta Selatan</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2 text-xs sm:text-sm"></i>
                            <span class="text-xs sm:text-sm">+62 21 1234 5678</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2 text-xs sm:text-sm"></i>
                            <span class="text-xs sm:text-sm">info@rt001.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-6 sm:mt-8 pt-6 sm:pt-8 text-center">
                <p class="text-gray-400 text-xs sm:text-sm">
                    &copy; {{ date('Y') }} Guyub Rukun Warga RT 02/RW 04 Kedurus - Kepatihan .
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add animation classes when elements come into view
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        // Observe all elements with animate class
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    </script>

    <!-- Map iframe removed: maps now load only on pages that need them to improve performance -->

    @yield('extra_js')
</body>
</html>
