<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#3b82f6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="RT App">

    <title>@yield('title', 'Guyub Rukun Warga - RT 02/RW 04 Kedurus - Kepatihan Kedurus Kepatihan')</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- Icons -->
    <link rel="apple-touch-icon" href="{{ asset('images/logo-rt.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/logo-rt.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'rt-green': '#4a7c59',
                        'rt-orange': '#ff8c00',
                        'rt-light-green': '#7fb069',
                        'rt-dark-green': '#2d5016'
                    }
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs"></script>

    <!-- Additional Styles Stack -->
    @stack('styles')

    <!-- Mobile Detection and PWA Setup -->
    <script>
        // Detect mobile and set appropriate layout
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;

        document.documentElement.classList.add(isMobile ? 'mobile' : 'desktop');

        // PWA Install prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            // Show install button for mobile users
            if (isMobile && !isStandalone) {
                showInstallPrompt();
            }
        });

        function showInstallPrompt() {
            const installBanner = document.createElement('div');
            installBanner.className = 'fixed bottom-4 left-4 right-4 rt-gradient text-white p-4 rounded-lg shadow-lg z-50 flex items-center justify-between';
            installBanner.innerHTML = `
                <div>
                    <p class="font-medium">Install RT App</p>
                    <p class="text-sm opacity-90">Akses lebih cepat dari home screen</p>
                </div>
                <button onclick="installApp()" class="bg-white text-rt-green px-3 py-1 rounded font-medium">Install</button>
                <button onclick="dismissInstall()" class="ml-2 text-white opacity-75">&times;</button>
            `;
            document.body.appendChild(installBanner);

            window.installBanner = installBanner;
        }

        function installApp() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('PWA installed');
                    }
                    deferredPrompt = null;
                    dismissInstall();
                });
            }
        }

        function dismissInstall() {
            if (window.installBanner) {
                window.installBanner.remove();
            }
        }

        // Register service worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('SW registered: ', registration);
                    })
                    .catch((registrationError) => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>

    <style>
        :root {
            --rt-green: #4a7c59;
            --rt-orange: #ff8c00;
            --rt-light-green: #7fb069;
            --rt-dark-green: #2d5016;
        }

        .rt-gradient {
            background: linear-gradient(135deg, var(--rt-green) 0%, var(--rt-orange) 100%);
        }

        .text-rt-green {
            color: var(--rt-green) !important;
        }

        /* Mobile-specific styles */
        .mobile body {
            padding-bottom: env(safe-area-inset-bottom);
        }

        .mobile .container {
            padding-left: 0;
            padding-right: 0;
            max-width: 100%;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800" x-data="appData()">
    <!-- Mobile vs Desktop Layout Detection -->
    @php
        $userAgent = request()->header('User-Agent');
        $isMobile = preg_match('/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i', $userAgent);
    @endphp

    @if($isMobile && auth()->check())
        <!-- Use mobile layout for authenticated mobile users -->
        @include('layouts.mobile-header')

        <main class="pb-16">
            @if(session('success'))
                <div class="mx-4 mt-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mx-4 mt-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

        @include('layouts.mobile-navigation')
    @else
        <!-- Desktop layout -->
        <header class="sticky top-0 bg-white border-b z-40" x-data="{ profileOpen: false, mobileMenuOpen: false }">
            <div class="container mx-auto px-4 py-3 flex items-center justify-between">
                <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-rt.png') }}" alt="Logo RT" class="w-8 h-8">
                    <div class="flex flex-col">
                        <span class="font-bold text-rt-green">Guyub Rukun Warga</span>
                        <span class="text-xs text-gray-600">RT 02/RW 04 Kedurus - Kepatihan </span>
                    </div>
                </a>

                @auth
                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 transition-colors">Dashboard</a>
                        @can('isAdmin')
                            <a href="{{ route('dashboard.analytics') }}" class="text-gray-700 hover:text-blue-600 transition-colors">Analytics</a>
                        @endcan

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 transition-colors">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-medium text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <span class="font-medium">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">

                                <!-- Profile Info -->
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                                    @can('isAdmin')
                                        <span class="inline-block mt-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Admin</span>
                                    @else
                                        <span class="inline-block mt-1 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Warga</span>
                                    @endcan
                                </div>

                                <!-- Menu Items -->
                                <div class="py-1">
                                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        </svg>
                                        Dashboard
                                    </a>

                                    @can('isAdmin')
                                        <a href="{{ route('dashboard.analytics') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            Analytics
                                        </a>
                                    @endcan

                                    <div class="border-t border-gray-100 my-1"></div>

                                    <form method="POST" action="/logout" class="block">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Hamburger Menu -->
                    <div class="md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x-show="!mobileMenuOpen" d="M4 6h16M4 12h16M4 18h16"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x-show="mobileMenuOpen" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile Menu Dropdown -->
                    <div x-show="mobileMenuOpen"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         @click.away="mobileMenuOpen = false"
                         class="absolute top-full right-4 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50 md:hidden">

                        <!-- Profile Info -->
                        <div class="px-4 py-3 border-b border-gray-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                    @can('isAdmin')
                                        <span class="inline-block mt-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Admin</span>
                                    @else
                                        <span class="inline-block mt-1 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Warga</span>
                                    @endcan
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002 2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                </svg>
                                Dashboard
                            </a>

                            @can('isAdmin')
                                <a href="{{ route('dashboard.analytics') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Analytics
                                </a>
                            @endcan

                            <div class="border-t border-gray-100 my-1"></div>

                            <form method="POST" action="/logout" class="block">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                        <a href="{{ route('login') }}" class="px-3 py-1 rounded rt-gradient text-white hover:opacity-90">Masuk</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="container mx-auto px-4 py-10">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="border-t bg-rt-green text-white">
            <div class="container mx-auto px-4 py-6 text-sm text-center">
                © {{ date('Y') }} Guyub Rukun Warga RT 02/RW 04
            </div>
        </footer>
    @endif

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        function appData() {
            return {
                // Global app state
                init() {
                    // Setup global toast function
                    window.showToast = this.showToast;
                }
            }
        }

        function showToast(message, type = 'info', duration = 3000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const bgColor = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'warning': 'bg-yellow-500',
                'info': 'bg-green-500'
            }[type] || 'bg-green-500';

            toast.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0`;
            toast.textContent = message;

            container.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 100);

            // Animate out and remove
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, duration);
        }
    </script>

    <!-- Additional Scripts Stack -->
    @stack('scripts')
</body>
</html>
