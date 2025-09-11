<!-- Google OAuth Warning Component -->
@if(env('GOOGLE_CLIENT_ID') === 'your_google_client_id_here' || !env('GOOGLE_CLIENT_ID'))
<div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">
                Google OAuth Belum Dikonfigurasi
            </h3>
            <div class="mt-2 text-sm text-yellow-700">
                <p>Untuk mengaktifkan login dengan Google, silakan:</p>
                <ol class="list-decimal list-inside mt-2 space-y-1">
                    <li>Buat Google OAuth credentials di <a href="https://console.cloud.google.com/" target="_blank" class="underline">Google Cloud Console</a></li>
                    <li>Update file <code class="bg-yellow-100 px-1 rounded">.env</code> dengan credentials yang valid</li>
                    <li>Jalankan <code class="bg-yellow-100 px-1 rounded">php artisan config:clear</code></li>
                </ol>
                <p class="mt-2">
                    Lihat panduan lengkap di file <code class="bg-yellow-100 px-1 rounded">GOOGLE_OAUTH_SETUP.md</code>
                </p>
            </div>
        </div>
    </div>
</div>
@endif
