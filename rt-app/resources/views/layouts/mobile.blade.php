<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="RT Manager">
    <meta name="application-name" content="RT Manager">
    <meta name="msapplication-TileColor" content="#1f2937">
    <meta name="theme-color" content="#1f2937">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- Icons for various devices -->
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/icon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
    <link rel="mask-icon" href="/icons/safari-pinned-tab.svg" color="#1f2937">

    <title>@yield('title', 'RT Manager')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Mobile-first CSS -->
    <style>
        /* Touch-friendly improvements */
        * {
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
            -webkit-touch-callout: none;
        }

        /* Better button touch targets */
        .btn-touch {
            min-height: 44px;
            min-width: 44px;
            touch-action: manipulation;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Swipe gestures */
        .swipeable {
            touch-action: pan-x;
        }

        /* Loading states */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Mobile navigation */
        .mobile-nav {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }

        .mobile-nav.open {
            transform: translateX(0);
        }

        /* Pull to refresh animation */
        .pull-to-refresh {
            transform: translateY(-100px);
            transition: transform 0.3s ease;
        }

        /* Floating action button */
        .fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        /* Card animations */
        .card-slide-in {
            animation: slideInUp 0.3s ease-out;
        }

        @keyframes slideInUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Responsive grid */
        .mobile-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: 1fr;
        }

        @media (min-width: 640px) {
            .mobile-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .mobile-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Status bar spacing for iOS */
        @@supports (padding: env(safe-area-inset-top)) {
            .ios-padding {
                padding-top: max(20px, env(safe-area-inset-top));
                padding-bottom: max(20px, env(safe-area-inset-bottom));
                padding-left: max(20px, env(safe-area-inset-left));
                padding-right: max(20px, env(safe-area-inset-right));
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .auto-dark {
                background-color: #1f2937;
                color: #f9fafb;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans" x-data="mobileApp()">
    <!-- PWA Install Banner -->
    <div x-show="showInstallPrompt"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="fixed top-0 left-0 right-0 bg-green-600 text-white p-4 z-50">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium">Instal RT Manager</p>
                <p class="text-xs opacity-90">Akses lebih cepat dan fitur offline</p>
            </div>
            <div class="flex space-x-2">
                <button @click="installPWA()" class="bg-white text-green-600 px-3 py-1 rounded text-sm font-medium">
                    Instal
                </button>
                <button @click="hideInstallPrompt()" class="text-white opacity-75">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Connection Status -->
    <div x-show="!isOnline"
         class="fixed top-0 left-0 right-0 bg-yellow-500 text-white p-2 text-center text-sm z-40">
        <span class="inline-block w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
        Mode Offline - Data akan disinkron otomatis
    </div>

    <!-- Mobile Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 ios-padding" :class="{'mt-16': showInstallPrompt, 'mt-10': !isOnline && !showInstallPrompt}">
        <div class="flex items-center justify-between p-4">
            <!-- Menu Button -->
            <button @click="toggleMobileMenu()" class="md:hidden btn-touch p-2 -ml-2 text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Logo -->
            <div class="flex items-center">
                <h1 class="text-xl font-bold text-gray-900">@yield('title', 'RT Manager')</h1>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2">
                <!-- Notifications -->
                <button class="btn-touch p-2 text-gray-600 relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h16a1 1 0 001-1V6a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1z"></path>
                    </svg>
                    <span x-show="unreadCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" x-text="unreadCount"></span>
                </button>

                <!-- Profile -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="btn-touch p-1">
                        <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=1f2937&color=fff" alt="Profile">
                    </button>

                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Overlay -->
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         @click="closeMobileMenu()"
         class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"></div>

    <!-- Mobile Navigation Sidebar -->
    <nav class="fixed left-0 top-0 h-full w-64 bg-white shadow-lg z-50 md:hidden mobile-nav"
         :class="{'open': mobileMenuOpen}">
        <div class="ios-padding">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Menu</h2>
                <button @click="closeMobileMenu()" class="btn-touch p-2 text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="p-4">
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-gray-100 btn-touch">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.households.index') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-gray-100 btn-touch">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Rumah Tangga
                    </a>
                    <a href="{{ route('admin.residents.index') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-gray-100 btn-touch">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Warga
                    </a>
                    <a href="{{ route('admin.letters.index') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-gray-100 btn-touch">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Surat
                    </a>
                    <a href="{{ route('admin.dues.index') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-gray-100 btn-touch">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Iuran
                    </a>
                </nav>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="ios-padding" :class="{'pt-20': showInstallPrompt || !isOnline}">
        @yield('content')
    </main>

    <!-- Floating Action Button -->
    <button @click="showQuickActions = !showQuickActions"
            class="fab btn-touch w-14 h-14 bg-green-600 text-white rounded-full flex items-center justify-center hover:bg-green-700 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
    </button>

    <!-- Quick Actions Menu -->
    <div x-show="showQuickActions"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         @click.away="showQuickActions = false"
         class="fixed bottom-20 right-5 bg-white rounded-lg shadow-lg p-2 z-50">
        <div class="space-y-2">
            <a href="{{ route('admin.letters.create') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-100 rounded-lg btn-touch">
                <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Buat Surat
            </a>
            <a href="{{ route('admin.residents.create') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-100 rounded-lg btn-touch">
                <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Warga
            </a>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="loading"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Memuat...</p>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-20 right-4 z-50 space-y-2"></div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        function mobileApp() {
            return {
                mobileMenuOpen: false,
                showInstallPrompt: false,
                showQuickActions: false,
                isOnline: navigator.onLine,
                loading: false,
                unreadCount: 0,
                deferredPrompt: null,

                init() {
                    this.setupPWA();
                    this.setupConnectionListener();
                    this.setupSwipeGestures();
                    this.loadOfflineData();
                },

                setupPWA() {
                    // Register service worker
                    if ('serviceWorker' in navigator) {
                        navigator.serviceWorker.register('/sw.js')
                            .then(registration => {
                                console.log('SW registered: ', registration);
                            })
                            .catch(registrationError => {
                                console.log('SW registration failed: ', registrationError);
                            });
                    }

                    // PWA install prompt
                    window.addEventListener('beforeinstallprompt', (e) => {
                        e.preventDefault();
                        this.deferredPrompt = e;
                        this.showInstallPrompt = true;
                    });
                },

                installPWA() {
                    if (this.deferredPrompt) {
                        this.deferredPrompt.prompt();
                        this.deferredPrompt.userChoice.then((choiceResult) => {
                            if (choiceResult.outcome === 'accepted') {
                                console.log('User accepted the install prompt');
                            }
                            this.deferredPrompt = null;
                            this.showInstallPrompt = false;
                        });
                    }
                },

                hideInstallPrompt() {
                    this.showInstallPrompt = false;
                },

                setupConnectionListener() {
                    window.addEventListener('online', () => {
                        this.isOnline = true;
                        this.showToast('Koneksi tersambung kembali', 'success');
                        this.syncOfflineData();
                    });

                    window.addEventListener('offline', () => {
                        this.isOnline = false;
                        this.showToast('Mode offline diaktifkan', 'warning');
                    });
                },

                setupSwipeGestures() {
                    let startX, startY, endX, endY;

                    document.addEventListener('touchstart', (e) => {
                        startX = e.touches[0].clientX;
                        startY = e.touches[0].clientY;
                    });

                    document.addEventListener('touchend', (e) => {
                        endX = e.changedTouches[0].clientX;
                        endY = e.changedTouches[0].clientY;

                        const deltaX = endX - startX;
                        const deltaY = endY - startY;

                        // Swipe right to open menu (on mobile)
                        if (deltaX > 100 && Math.abs(deltaY) < 50 && startX < 50) {
                            this.openMobileMenu();
                        }

                        // Swipe left to close menu
                        if (deltaX < -100 && Math.abs(deltaY) < 50 && this.mobileMenuOpen) {
                            this.closeMobileMenu();
                        }
                    });
                },

                toggleMobileMenu() {
                    this.mobileMenuOpen = !this.mobileMenuOpen;
                },

                openMobileMenu() {
                    this.mobileMenuOpen = true;
                },

                closeMobileMenu() {
                    this.mobileMenuOpen = false;
                },

                showToast(message, type = 'info') {
                    const container = document.getElementById('toast-container');
                    const toast = document.createElement('div');

                    const colors = {
                        success: 'bg-green-500',
                        error: 'bg-red-500',
                        warning: 'bg-yellow-500',
                        info: 'bg-green-500'
                    };

                    toast.className = `${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
                    toast.textContent = message;

                    container.appendChild(toast);

                    // Animate in
                    setTimeout(() => {
                        toast.classList.remove('translate-x-full');
                    }, 100);

                    // Animate out and remove
                    setTimeout(() => {
                        toast.classList.add('translate-x-full');
                        setTimeout(() => {
                            container.removeChild(toast);
                        }, 300);
                    }, 3000);
                },

                loadOfflineData() {
                    // Load any cached data when offline
                    if (!this.isOnline) {
                        // Implementation for loading offline data
                    }
                },

                syncOfflineData() {
                    // Sync offline data when back online
                    if (this.isOnline) {
                        // Implementation for syncing offline data
                    }
                }
            }
        }

        // Save current page for offline access
        localStorage.setItem('lastVisitedPage', window.location.pathname);
    </script>

    @stack('scripts')
</body>
</html>
