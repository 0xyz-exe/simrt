@props(['title', 'subtitle' => null, 'backUrl' => null, 'actions' => null])

<div class="bg-white border-b border-gray-200 px-4 py-3 sticky top-0 z-40">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            @if($backUrl)
                <a href="{{ $backUrl }}" class="btn-touch p-2 -ml-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
            @endif

            <div>
                <h1 class="text-lg font-semibold text-gray-900">{{ $title }}</h1>
                @if($subtitle)
                    <p class="text-sm text-gray-600">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if($actions)
            <div class="flex items-center space-x-2">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
