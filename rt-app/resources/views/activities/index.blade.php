@extends('layouts.app')
@section('title', 'Kelola Kegiatan')
@section('content')
    <!-- Back to Dashboard Button -->
    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Kelola Kegiatan</h1>
            <p class="text-slate-600">Buat dan kelola kegiatan untuk warga RT</p>
        </div>
        <a href="{{ route('activities.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Tambah Kegiatan
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-wrap gap-4">
            <select class="border rounded px-3 py-2" onchange="filterActivities(this.value)">
                <option value="">Semua Status</option>
                <option value="scheduled">Dijadwalkan</option>
                <option value="ongoing">Sedang Berlangsung</option>
                <option value="completed">Selesai</option>
                <option value="cancelled">Dibatalkan</option>
            </select>

            <select class="border rounded px-3 py-2" onchange="filterByType(this.value)">
                <option value="">Semua Jenis</option>
                <option value="gotong_royong">Gotong Royong</option>
                <option value="rapat">Rapat RT</option>
                <option value="acara_khusus">Acara Khusus</option>
                <option value="olahraga">Olahraga</option>
                <option value="sosial">Sosial</option>
                <option value="keagamaan">Keagamaan</option>
                <option value="pendidikan">Pendidikan</option>
                <option value="kesehatan">Kesehatan</option>
            </select>
        </div>
    </div>

    <!-- Activities List -->
    <div class="grid gap-6">
        @forelse($activities as $activity)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden activity-card"
                 data-status="{{ $activity->status }}"
                 data-type="{{ $activity->type }}">
                <div class="md:flex">
                    <div class="md:w-48 h-48 md:h-auto">
                        @if($activity->image)
                            <img src="{{ Storage::url($activity->image) }}"
                                 alt="{{ $activity->title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-white text-4xl"></i>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $activity->title }}</h3>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $activity->status_badge }}">
                                        {{ $activity->status_text }}
                                    </span>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                        {{ $activity->type_text }}
                                    </span>
                                    @if($activity->is_featured)
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                            <i class="fas fa-star mr-1"></i>Unggulan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('activities.edit', $activity) }}"
                                   class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('activities.destroy', $activity) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $activity->description }}</p>

                        <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-600">
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-day w-4 mr-2 text-blue-600"></i>
                                    <span>{{ $activity->formatted_date }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock w-4 mr-2 text-green-600"></i>
                                    <span>{{ $activity->formatted_time }}</span>
                                </div>
                                @if($activity->location)
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt w-4 mr-2 text-red-600"></i>
                                        <span>{{ $activity->location }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-2">
                                @if($activity->max_participants)
                                    <div class="flex items-center">
                                        <i class="fas fa-users w-4 mr-2 text-purple-600"></i>
                                        <span>{{ $activity->current_participants }}/{{ $activity->max_participants }} peserta</span>
                                    </div>
                                @endif
                                @if($activity->contact_person)
                                    <div class="flex items-center">
                                        <i class="fas fa-user w-4 mr-2 text-orange-600"></i>
                                        <span>{{ $activity->contact_person }}</span>
                                    </div>
                                @endif
                                @if($activity->contact_phone)
                                    <div class="flex items-center">
                                        <i class="fas fa-phone w-4 mr-2 text-green-600"></i>
                                        <span>{{ $activity->contact_phone }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 mt-4">
                            <form action="{{ route('activities.toggle-status', $activity) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1 text-xs rounded {{ $activity->is_active ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                                    {{ $activity->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>

                            <form action="{{ route('activities.toggle-featured', $activity) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1 text-xs rounded {{ $activity->is_featured ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                    {{ $activity->is_featured ? 'Hapus Unggulan' : 'Jadikan Unggulan' }}
                                </button>
                            </form>

                            <a href="{{ route('activities.show', $activity) }}"
                               class="px-3 py-1 text-xs rounded bg-blue-100 text-blue-800 hover:bg-blue-200">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Kegiatan</h3>
                <p class="text-gray-500 mb-6">Tambahkan kegiatan pertama untuk warga RT</p>
                <a href="{{ route('activities.create') }}"
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Kegiatan
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($activities->hasPages())
        <div class="mt-8">
            {{ $activities->links() }}
        </div>
    @endif
@endsection

@push('scripts')
<script>
    function filterActivities(status) {
        const cards = document.querySelectorAll('.activity-card');
        cards.forEach(card => {
            if (status === '' || card.dataset.status === status) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function filterByType(type) {
        const cards = document.querySelectorAll('.activity-card');
        cards.forEach(card => {
            if (type === '' || card.dataset.type === type) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endpush
