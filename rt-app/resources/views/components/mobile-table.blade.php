@props(['data', 'columns', 'actions' => null, 'searchable' => true])

<div class="mobile-table" x-data="mobileTable()">
    @if($searchable)
        <!-- Search Bar -->
        <div class="bg-white p-4 border-b border-gray-200 sticky top-16 z-30">
            <div class="relative">
                <input type="text"
                       x-model="searchQuery"
                       @input="filterData()"
                       placeholder="Cari data..."
                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
    @endif

    <!-- Data Cards -->
    <div class="divide-y divide-gray-200">
        <template x-for="(item, index) in filteredData" :key="index">
            <div class="bg-white p-4 hover:bg-gray-50 transition-colors"
                 @swiperight="handleSwipe(item, 'right')"
                 @swipeleft="handleSwipe(item, 'left')">

                <!-- Card Content -->
                <div class="space-y-2">
                    @foreach($columns as $key => $column)
                        @if(is_array($column))
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ $column['label'] }}</span>
                                @if($column['type'] === 'badge')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          :class="getBadgeClass(item.{{ $key }})">
                                        <span x-text="item.{{ $key }}"></span>
                                    </span>
                                @elseif($column['type'] === 'currency')
                                    <span class="text-sm font-medium text-gray-900" x-text="formatCurrency(item.{{ $key }})"></span>
                                @elseif($column['type'] === 'date')
                                    <span class="text-sm font-medium text-gray-900" x-text="formatDate(item.{{ $key }})"></span>
                                @else
                                    <span class="text-sm font-medium text-gray-900" x-text="item.{{ $key }}"></span>
                                @endif
                            </div>
                        @else
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ $column }}</span>
                                <span class="text-sm font-medium text-gray-900" x-text="item.{{ $key }}"></span>
                            </div>
                        @endif
                    @endforeach
                </div>

                @if($actions)
                    <!-- Actions -->
                    <div class="flex justify-end space-x-2 mt-3 pt-3 border-t border-gray-100">
                        {{ $actions }}
                    </div>
                @endif

                <!-- Swipe Indicator -->
                <div class="swipe-indicator" x-show="swipeDirection"
                     :class="swipeDirection === 'left' ? 'swipe-left' : 'swipe-right'">
                    <span x-text="swipeDirection === 'left' ? 'Hapus' : 'Edit'"></span>
                </div>
            </div>
        </template>

        <!-- Empty State -->
        <div x-show="filteredData.length === 0" class="p-8 text-center">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-500">Tidak ada data ditemukan</p>
        </div>
    </div>

    <!-- Load More -->
    <div x-show="hasMore" class="p-4 text-center border-t border-gray-200">
        <button @click="loadMore()"
                class="w-full py-3 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors btn-touch"
                :disabled="loading">
            <span x-show="!loading">Muat Lebih Banyak</span>
            <span x-show="loading" class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memuat...
            </span>
        </button>
    </div>
</div>

<script>
function mobileTable() {
    return {
        searchQuery: '',
        filteredData: @json($data),
        originalData: @json($data),
        swipeDirection: null,
        loading: false,
        hasMore: false,
        currentPage: 1,

        init() {
            this.setupSwipeGestures();
        },

        filterData() {
            if (!this.searchQuery) {
                this.filteredData = this.originalData;
                return;
            }

            this.filteredData = this.originalData.filter(item => {
                return Object.values(item).some(value =>
                    String(value).toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            });
        },

        setupSwipeGestures() {
            let startX = 0;
            let startY = 0;
            let currentX = 0;
            let currentY = 0;
            let swiping = false;

            document.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
                swiping = true;
            });

            document.addEventListener('touchmove', (e) => {
                if (!swiping) return;

                currentX = e.touches[0].clientX;
                currentY = e.touches[0].clientY;

                const diffX = Math.abs(currentX - startX);
                const diffY = Math.abs(currentY - startY);

                // Only horizontal swipes
                if (diffX > diffY && diffX > 50) {
                    const direction = currentX > startX ? 'right' : 'left';
                    this.swipeDirection = direction;
                }
            });

            document.addEventListener('touchend', () => {
                swiping = false;
                this.swipeDirection = null;
            });
        },

        handleSwipe(item, direction) {
            // Emit custom events for parent components to handle
            if (direction === 'left') {
                this.$dispatch('item-delete', item);
            } else if (direction === 'right') {
                this.$dispatch('item-edit', item);
            }
        },

        getBadgeClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'approved': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800',
                'paid': 'bg-green-100 text-green-800',
                'unpaid': 'bg-red-100 text-red-800',
                'partial': 'bg-yellow-100 text-yellow-800',
                'active': 'bg-green-100 text-green-800',
                'inactive': 'bg-gray-100 text-gray-800'
            };
            return classes[status.toLowerCase()] || 'bg-gray-100 text-gray-800';
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        },

        async loadMore() {
            this.loading = true;
            try {
                // In real app, fetch more data from API
                await new Promise(resolve => setTimeout(resolve, 1000));

                // Simulate loading more data
                this.currentPage++;
                // Add new data to filteredData

                this.loading = false;
            } catch (error) {
                this.loading = false;
                console.error('Error loading more data:', error);
            }
        }
    }
}
</script>

<style>
.swipe-indicator {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    padding: 8px 16px;
    border-radius: 8px;
    color: white;
    font-size: 14px;
    font-weight: 500;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.swipe-indicator.swipe-left {
    right: 16px;
    background-color: #ef4444;
    opacity: 1;
}

.swipe-indicator.swipe-right {
    left: 16px;
    background-color: #3b82f6;
    opacity: 1;
}
</style>
@endsection
