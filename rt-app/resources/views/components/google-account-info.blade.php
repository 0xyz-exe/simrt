<!-- Google Account Info Component -->
@if(Auth::user()->google_id)
<div class="bg-gradient-to-r from-green-50 to-orange-50 border border-green-200 rounded-xl p-6 mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <!-- User Avatar -->
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="Profile" class="w-12 h-12 rounded-full border-2 border-white shadow-lg">
            @else
                <div class="w-12 h-12 bg-gradient-to-r rt-gradient rounded-full flex items-center justify-center">
                    <span class="text-white font-semibold text-lg">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
            @endif

            <!-- Account Info -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Terhubung dengan Google
                </h3>
                <p class="text-sm text-gray-600">Akun Anda terhubung dengan {{ Auth::user()->email }}</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-3">
            @if(Auth::user()->password)
                <!-- Show unlink option if user has password -->
                <form action="{{ route('auth.google.unlink') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Yakin ingin memutus hubungan dengan Google? Anda masih bisa login dengan email dan password.')"
                            class="text-red-600 hover:text-red-700 text-sm font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Putus Hubungan
                    </button>
                </form>
            @else
                <!-- Show warning if no password -->
                <div class="text-orange-600 text-sm font-medium flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Akun khusus Google
                </div>
            @endif

            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Terverifikasi
            </span>
        </div>
    </div>
</div>
@endif
