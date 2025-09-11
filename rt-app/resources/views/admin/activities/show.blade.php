@extends('admin.layouts.app')
@section('title', 'Detail Kegiatan')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Kegiatan</h1>
        <div>
            <a href="{{ route('admin.activities.edit', $activity) }}" class="btn btn-primary">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit
            </a>
            <a href="{{ route('admin.activities.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Activity Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $activity->title }}</h6>
                    <div>
                        <span class="badge badge-{{ $activity->status == 'published' ? 'success' : ($activity->status == 'draft' ? 'secondary' : ($activity->status == 'cancelled' ? 'danger' : 'primary')) }}">
                            {{ $activity->status_text }}
                        </span>
                        @if($activity->is_featured)
                            <span class="badge badge-warning">
                                <i class="fas fa-star"></i> Unggulan
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($activity->image)
                        <div class="mb-4">
                            <img src="{{ Storage::url($activity->image) }}"
                                 alt="{{ $activity->title }}"
                                 class="img-fluid rounded"
                                 style="max-height: 400px; width: 100%; object-fit: cover;">
                        </div>
                    @endif

                    <div class="mb-4">
                        <h5 class="font-weight-bold text-gray-800">Deskripsi</h5>
                        <p class="text-gray-700">{!! nl2br(e($activity->description)) !!}</p>
                    </div>

                    @if($activity->requirements)
                        <div class="mb-4">
                            <h5 class="font-weight-bold text-gray-800">Yang Perlu Dibawa</h5>
                            <div class="alert alert-warning">
                                {!! nl2br(e($activity->requirements)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.activities.edit', $activity) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Kegiatan
                        </a>

                        @if($activity->status == 'published')
                            <a href="{{ route('landing.activity-detail', $activity) }}"
                               target="_blank"
                               class="btn btn-success">
                                <i class="fas fa-eye"></i> Lihat di Website
                            </a>
                        @endif

                        <form action="{{ route('admin.activities.toggle-status', $activity) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="btn btn-{{ $activity->is_active ? 'warning' : 'success' }}">
                                <i class="fas fa-{{ $activity->is_active ? 'pause' : 'play' }}"></i>
                                {{ $activity->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.activities.toggle-featured', $activity) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="btn btn-{{ $activity->is_featured ? 'secondary' : 'warning' }}">
                                <i class="fas fa-star"></i>
                                {{ $activity->is_featured ? 'Batal Unggulan' : 'Jadikan Unggulan' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- Basic Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Dasar</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="font-weight-bold">Jenis:</td>
                            <td>{{ $activity->type_text }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Status:</td>
                            <td>
                                <span class="badge badge-{{ $activity->status == 'published' ? 'success' : ($activity->status == 'draft' ? 'secondary' : ($activity->status == 'cancelled' ? 'danger' : 'primary')) }}">
                                    {{ $activity->status_text }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Tanggal:</td>
                            <td>{{ $activity->formatted_date }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Waktu:</td>
                            <td>{{ $activity->formatted_time }}</td>
                        </tr>
                        @if($activity->location)
                            <tr>
                                <td class="font-weight-bold">Lokasi:</td>
                                <td>{{ $activity->location }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="font-weight-bold">Dibuat:</td>
                            <td>{{ $activity->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Diupdate:</td>
                            <td>{{ $activity->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Participants Info -->
            @if($activity->max_participants)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informasi Peserta</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-gray-700">Peserta Terdaftar</span>
                                <span class="font-weight-bold">{{ $activity->current_participants }}/{{ $activity->max_participants }}</span>
                            </div>
                            @php
                                $percentage = $activity->max_participants > 0 ? ($activity->current_participants / $activity->max_participants) * 100 : 0;
                                $progressClass = $percentage >= 80 ? 'danger' : ($percentage >= 50 ? 'warning' : 'success');
                            @endphp
                            <div class="progress">
                                <div class="progress-bar bg-{{ $progressClass }}"
                                     role="progressbar"
                                     style="width: {{ $percentage }}%"
                                     aria-valuenow="{{ $percentage }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                    {{ number_format($percentage, 1) }}%
                                </div>
                            </div>
                        </div>

                        @if($activity->participation_status)
                            <div class="alert alert-info alert-sm mb-0">
                                <strong>Status Pendaftaran:</strong><br>
                                {{ ucfirst($activity->participation_status) }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Contact Info -->
            @if($activity->contact_person || $activity->contact_phone)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Koordinator</h6>
                    </div>
                    <div class="card-body">
                        @if($activity->contact_person)
                            <p class="mb-2">
                                <i class="fas fa-user text-primary mr-2"></i>
                                <strong>{{ $activity->contact_person }}</strong>
                            </p>
                        @endif

                        @if($activity->contact_phone)
                            <p class="mb-3">
                                <i class="fas fa-phone text-success mr-2"></i>
                                {{ $activity->contact_phone }}
                            </p>

                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $activity->contact_phone) }}"
                               target="_blank"
                               class="btn btn-success btn-sm btn-block">
                                <i class="fab fa-whatsapp"></i> Chat WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik</h6>
                </div>
                <div class="card-body">
                    <div class="row no-gutters">
                        <div class="col-6 border-right">
                            <div class="text-center">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Views</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ rand(10, 100) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Shares</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ rand(1, 20) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Action -->
            <div class="card shadow border-left-danger">
                <div class="card-body">
                    <h6 class="font-weight-bold text-danger">Zona Berbahaya</h6>
                    <p class="text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan.</p>
                    <button type="button"
                            class="btn btn-danger btn-sm"
                            data-toggle="modal"
                            data-target="#deleteModal">
                        <i class="fas fa-trash"></i> Hapus Kegiatan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kegiatan "<strong>{{ $activity->title }}</strong>"?</p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{ route('admin.activities.destroy', $activity) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
