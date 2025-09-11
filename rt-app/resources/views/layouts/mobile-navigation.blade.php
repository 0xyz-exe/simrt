<!-- Mobile Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-30" x-data="mobileNavigation()">
    <div class="grid {{ auth()->user()->is_admin ? 'grid-cols-5' : 'grid-cols-4' }} h-16">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="flex flex-col items-center justify-center space-y-1 btn-touch"
           :class="isActive('dashboard') ? 'text-green-600 bg-green-50' : 'text-gray-600'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
            </svg>
            <span class="text-xs">Dashboard</span>
        </a>

        @if(auth()->user()->is_admin)
            <!-- Data Management -->
            <a href="{{ route('residents.index') }}"
               class="flex flex-col items-center justify-center space-y-1 btn-touch"
               :class="isActive('residents') || isActive('households') ? 'text-green-600 bg-green-50' : 'text-gray-600'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <span class="text-xs">Data</span>
            </a>

            <!-- UMKM Management -->
            <a href="{{ route('admin.businesses.index') }}"
               class="flex flex-col items-center justify-center space-y-1 btn-touch"
               :class="isActive('admin.businesses') ? 'text-green-600 bg-green-50' : 'text-gray-600'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span class="text-xs">UMKM</span>
            </a>

            <!-- Financial -->
            <a href="{{ route('dues.index') }}"
               class="flex flex-col items-center justify-center space-y-1 btn-touch"
               :class="isActive('dues') || isActive('payments') ? 'text-green-600 bg-green-50' : 'text-gray-600'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                <span class="text-xs">Keuangan</span>
            </a>

            <!-- Letters -->
            <a href="{{ route('letters.index') }}"
               class="flex flex-col items-center justify-center space-y-1 btn-touch"
               :class="isActive('letters') ? 'text-green-600 bg-green-50' : 'text-gray-600'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-xs">Surat</span>
            </a>
        @else
            <!-- My Data -->
            <a href="{{ route('households.mine') }}"
               class="flex flex-col items-center justify-center space-y-1 btn-touch"
               :class="isActive('households.mine') ? 'text-green-600 bg-green-50' : 'text-gray-600'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                </svg>
                <span class="text-xs">Data KK</span>
            </a>

            <!-- My Dues -->
            <a href="{{ route('dues.mine') }}"
               class="flex flex-col items-center justify-center space-y-1 btn-touch"
               :class="isActive('dues.mine') ? 'text-green-600 bg-green-50' : 'text-gray-600'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                <span class="text-xs">Iuran</span>
            </a>

            <!-- My Letters -->
            <a href="{{ route('letters.mine') }}"
               class="flex flex-col items-center justify-center space-y-1 btn-touch"
               :class="isActive('letters.mine') ? 'text-green-600 bg-green-50' : 'text-gray-600'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-xs">Surat</span>
            </a>
        @endif
    </div>

    <!-- Floating Action Button -->
    <div class="absolute -top-8 right-4">
        <button @click="showQuickActions()"
                class="w-14 h-14 bg-green-600 text-white rounded-full shadow-lg flex items-center justify-center btn-touch transform transition-transform hover:scale-105">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        </button>
    </div>

    <!-- Quick Actions Modal -->
    <div x-show="quickActionsOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 transform translate-y-4 scale-95"
         class="fixed inset-0 z-50 flex items-end justify-center p-4"
         @click.away="closeQuickActions()">

        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Modal -->
        <div class="relative w-full max-w-sm bg-white rounded-t-3xl shadow-xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                    <button @click="closeQuickActions()" class="btn-touch p-2 -mr-2">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('residents.create') }}"
                           class="flex flex-col items-center p-4 bg-green-50 rounded-xl hover:bg-blue-100 transition-colors btn-touch"
                           @click="closeQuickActions()">
                            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Tambah Warga</span>
                        </a>

                        <a href="{{ route('dues.index') }}"
                           class="flex flex-col items-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors btn-touch"
                           @click="closeQuickActions()">
                            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Kelola Iuran</span>
                        </a>

                        <a href="{{ route('letters.index') }}"
                           class="flex flex-col items-center p-4 bg-yellow-50 rounded-xl hover:bg-yellow-100 transition-colors btn-touch"
                           @click="closeQuickActions()">
                            <div class="w-12 h-12 bg-yellow-600 rounded-lg flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Kelola Surat</span>
                        </a>

                        <a href="{{ route('admin.businesses.create') }}"
                           class="flex flex-col items-center p-4 bg-orange-50 rounded-xl hover:bg-orange-100 transition-colors btn-touch"
                           @click="closeQuickActions()">
                            <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Tambah UMKM</span>
                        </a>
                    @else
                        <a href="{{ route('letters.create') }}"
                           class="flex flex-col items-center p-4 bg-green-50 rounded-xl hover:bg-blue-100 transition-colors btn-touch"
                           @click="closeQuickActions()">
                            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Ajukan Surat</span>
                        </a>

                        <a href="{{ route('dues.mine') }}"
                           class="flex flex-col items-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors btn-touch"
                           @click="closeQuickActions()">
                            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Cek Iuran</span>
                        </a>

                        <a href="{{ route('households.mine') }}"
                           class="flex flex-col items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors btn-touch"
                           @click="closeQuickActions()">
                            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Data KK</span>
                        </a>

                        <a href="{{ route('landing.businesses') }}"
                           class="flex flex-col items-center p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors btn-touch"
                           @click="closeQuickActions()">
                            <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-900">UMKM Warga</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
function mobileNavigation() {
    return {
        quickActionsOpen: false,

        isActive(routeName) {
            const currentPath = window.location.pathname;

            // Handle special cases for specific route patterns
            if (routeName === 'admin.businesses') {
                return currentPath.includes('/admin/businesses');
            }

            if (routeName === 'residents') {
                return currentPath.includes('/residents');
            }

            if (routeName === 'households') {
                return currentPath.includes('/households');
            }

            if (routeName === 'dues') {
                return currentPath.includes('/dues') || currentPath.includes('/payments');
            }

            if (routeName === 'letters') {
                return currentPath.includes('/letters');
            }

            if (routeName === 'dashboard') {
                return currentPath === '/dashboard' || currentPath === '/';
            }

            // Default behavior
            return currentPath.includes(routeName);
        },

        showQuickActions() {
            this.quickActionsOpen = true;
        },

        closeQuickActions() {
            this.quickActionsOpen = false;
        }
    }
}
</script>
