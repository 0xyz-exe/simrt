<!-- Mobile Header -->
<header class="bg-white border-b border-gray-200 sticky top-0 z-40" x-data="mobileHeader()">
    <div class="flex items-center justify-between px-4 py-3">
        <!-- Menu Toggle -->
        <button @click="toggleSidebar()" class="btn-touch p-2 -ml-2">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Logo/Title -->
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">RT</span>
            </div>
            <span class="font-semibold text-gray-900">Sistem RT</span>
        </div>

        <!-- Profile/Actions -->
        <div class="flex items-center space-x-2">
            <!-- Notifications -->
            <button @click="showNotifications()" class="btn-touch p-2 relative">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5V3h0z"></path>
                </svg>
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
            </button>

            <!-- Profile -->
            <button @click="toggleProfile()" class="btn-touch p-1">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 font-medium text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
            </button>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-40"
         @click="closeSidebar()"></div>

    <!-- Sidebar -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed top-0 left-0 z-50 w-80 h-full bg-white shadow-lg">

        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-blue-600 text-white">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-lg">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div>
                    <h3 class="font-semibold text-white">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-blue-100">{{ auth()->user()->email }}</p>
                    <span class="inline-block mt-1 px-2 py-1 bg-white bg-opacity-20 text-white text-xs rounded-full">
                        {{ auth()->user()->is_admin ? 'Administrator' : 'Warga' }}
                    </span>
                </div>
            </div>
            <button @click="closeSidebar()" class="btn-touch p-2 text-white hover:bg-white hover:bg-opacity-20 rounded">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="p-4 space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors"
               @click="closeSidebar()">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                </svg>
                <span class="text-gray-900">Dashboard</span>
            </a>

            @if(auth()->user()->is_admin)
                <!-- Admin Menu -->
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Administrasi</p>

                    <a href="{{ route('households.index') }}"
                       class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors"
                       @click="closeSidebar()">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        <span class="text-gray-900">Kelola KK</span>
                    </a>

                    <a href="{{ route('residents.index') }}"
                       class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors"
                       @click="closeSidebar()">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <span class="text-gray-900">Kelola Warga</span>
                    </a>

                    <a href="{{ route('dues.index') }}"
                       class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors"
                       @click="closeSidebar()">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span class="text-gray-900">Kelola Iuran</span>
                    </a>

                    <a href="{{ route('letters.index') }}"
                       class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors"
                       @click="closeSidebar()">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-gray-900">Kelola Surat</span>
                    </a>

                    <a href="{{ route('dashboard.analytics') }}"
                       class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors"
                       @click="closeSidebar()">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="text-gray-900">Analytics</span>
                    </a>

                    <a href="{{ route('announcements.index') }}"
                       class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors"
                       @click="closeSidebar()">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                        <span class="text-gray-900">Pengumuman</span>
                    </a>

                    <a href="{{ route('announcements.index') }}"
                       class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors"
                       @click="closeSidebar()">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                        <span class="text-gray-900">Pengumuman</span>
                    </a>
                </div>
            @else
                <!-- Resident Menu -->
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Menu Warga</p>

                    <a href="{{ route('households.mine') }}"
                       class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors"
                       @click="closeSidebar()">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        <span class="text-gray-900">Data KK Saya</span>
                    </a>

                    <a href="{{ route('dues.mine') }}"
                       class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors"
                       @click="closeSidebar()">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span class="text-gray-900">Iuran Saya</span>
                    </a>

                    <a href="{{ route('letters.mine') }}"
                       class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors"
                       @click="closeSidebar()">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-gray-900">Surat Saya</span>
                    </a>

                    <a href="{{ route('letters.create') }}"
                       class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors"
                       @click="closeSidebar()">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="text-gray-900">Ajukan Surat</span>
                    </a>
                </div>
            @endif

            <!-- Common Menu -->
            <div class="border-t border-gray-200 pt-4 mt-4">
                <a href="{{ route('landing.announcements') }}"
                   class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors"
                   @click="closeSidebar()">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                    <span class="text-gray-900">Pengumuman</span>
                </a>

                <form method="POST" action="/logout" @submit="closeSidebar()">
                    @csrf
                    <button type="submit" class="w-full flex items-center space-x-3 p-3 rounded-lg hover:bg-red-50 text-red-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </div>

    <!-- Profile Dropdown -->
    <div x-show="profileOpen"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-4 top-16 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
         @click.away="closeProfile()">

        <div class="p-4 border-b border-gray-200">
            <p class="font-medium text-gray-900">{{ auth()->user()->name }}</p>
            <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
        </div>

        <div class="p-2">
            <a href="#" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                Pengaturan
            </a>
            <a href="#" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                Bantuan
            </a>
            <form method="POST" action="/logout" class="mt-2 pt-2 border-t border-gray-200">
                @csrf
                <button type="submit" class="block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded">
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>

<script>
function mobileHeader() {
    return {
        sidebarOpen: false,
        profileOpen: false,

        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            this.profileOpen = false;
        },

        closeSidebar() {
            this.sidebarOpen = false;
        },

        toggleProfile() {
            this.profileOpen = !this.profileOpen;
            this.sidebarOpen = false;
        },

        closeProfile() {
            this.profileOpen = false;
        },

        showNotifications() {
            // Implement notifications modal
            alert('Notifications feature coming soon!');
        }
    }
}
</script>
