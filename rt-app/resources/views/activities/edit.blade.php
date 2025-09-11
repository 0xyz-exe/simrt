@extends('admin.layouts.app')
@section('title', 'Edit Kegiatan')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Kegiatan</h1>
        <a href="{{ route('admin.activities.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit Kegiatan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.activities.update', $activity) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-lg-8">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Informasi Dasar</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Title -->
                                        <div class="form-group">
                                            <label for="title">Judul Kegiatan <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control @error('title') is-invalid @enderror"
                                                   id="title"
                                                   name="title"
                                                   value="{{ old('title', $activity->title) }}"
                                                   required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Description -->
                                        <div class="form-group">
                                            <label for="description">Deskripsi <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('description') is-invalid @enderror"
                                                      id="description"
                                                      name="description"
                                                      rows="6"
                                                      required>{{ old('description', $activity->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Type -->
                                        <div class="form-group">
                                            <label for="type">Jenis Kegiatan <span class="text-danger">*</span></label>
                                            <select class="form-control @error('type') is-invalid @enderror"
                                                    id="type"
                                                    name="type"
                                                    required>
                                                <option value="">-- Pilih Jenis --</option>
                                                <option value="social" {{ old('type', $activity->type) == 'social' ? 'selected' : '' }}>Sosial</option>
                                                <option value="religious" {{ old('type', $activity->type) == 'religious' ? 'selected' : '' }}>Keagamaan</option>
                                                <option value="community" {{ old('type', $activity->type) == 'community' ? 'selected' : '' }}>Komunitas</option>
                                                <option value="sports" {{ old('type', $activity->type) == 'sports' ? 'selected' : '' }}>Olahraga</option>
                                                <option value="education" {{ old('type', $activity->type) == 'education' ? 'selected' : '' }}>Edukasi</option>
                                                <option value="health" {{ old('type', $activity->type) == 'health' ? 'selected' : '' }}>Kesehatan</option>
                                                <option value="environment" {{ old('type', $activity->type) == 'environment' ? 'selected' : '' }}>Lingkungan</option>
                                                <option value="meeting" {{ old('type', $activity->type) == 'meeting' ? 'selected' : '' }}>Rapat</option>
                                                <option value="other" {{ old('type', $activity->type) == 'other' ? 'selected' : '' }}>Lainnya</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Dates and Times -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="start_date">Tanggal Mulai <span class="text-danger">*</span></label>
                                                    <input type="date"
                                                           class="form-control @error('start_date') is-invalid @enderror"
                                                           id="start_date"
                                                           name="start_date"
                                                           value="{{ old('start_date', $activity->start_date ? $activity->start_date->format('Y-m-d') : '') }}"
                                                           required>
                                                    @error('start_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="end_date">Tanggal Selesai</label>
                                                    <input type="date"
                                                           class="form-control @error('end_date') is-invalid @enderror"
                                                           id="end_date"
                                                           name="end_date"
                                                           value="{{ old('end_date', $activity->end_date ? $activity->end_date->format('Y-m-d') : '') }}">
                                                    @error('end_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="start_time">Waktu Mulai <span class="text-danger">*</span></label>
                                                    <input type="time"
                                                           class="form-control @error('start_time') is-invalid @enderror"
                                                           id="start_time"
                                                           name="start_time"
                                                           value="{{ old('start_time', $activity->start_time ? $activity->start_time->format('H:i') : '') }}"
                                                           required>
                                                    @error('start_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="end_time">Waktu Selesai</label>
                                                    <input type="time"
                                                           class="form-control @error('end_time') is-invalid @enderror"
                                                           id="end_time"
                                                           name="end_time"
                                                           value="{{ old('end_time', $activity->end_time ? $activity->end_time->format('H:i') : '') }}">
                                                    @error('end_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Location -->
                                        <div class="form-group">
                                            <label for="location">Lokasi</label>
                                            <input type="text"
                                                   class="form-control @error('location') is-invalid @enderror"
                                                   id="location"
                                                   name="location"
                                                   value="{{ old('location', $activity->location) }}"
                                                   placeholder="Contoh: Balai RT, Masjid Al-Ikhlas, dll">
                                            @error('location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Requirements -->
                                        <div class="form-group">
                                            <label for="requirements">Yang Perlu Dibawa</label>
                                            <textarea class="form-control @error('requirements') is-invalid @enderror"
                                                      id="requirements"
                                                      name="requirements"
                                                      rows="3"
                                                      placeholder="Contoh: Alat tulis, makanan ringan, dll">{{ old('requirements', $activity->requirements) }}</textarea>
                                            @error('requirements')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings & Contact -->
                            <div class="col-lg-4">
                                <!-- Settings -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Pengaturan</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Status -->
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control @error('status') is-invalid @enderror"
                                                    id="status"
                                                    name="status">
                                                <option value="draft" {{ old('status', $activity->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                                <option value="published" {{ old('status', $activity->status) == 'published' ? 'selected' : '' }}>Dipublikasi</option>
                                                <option value="cancelled" {{ old('status', $activity->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                                <option value="completed" {{ old('status', $activity->status) == 'completed' ? 'selected' : '' }}>Selesai</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Featured -->
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox"
                                                       class="custom-control-input @error('is_featured') is-invalid @enderror"
                                                       id="is_featured"
                                                       name="is_featured"
                                                       value="1"
                                                       {{ old('is_featured', $activity->is_featured) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_featured">
                                                    Kegiatan Unggulan
                                                    <small class="text-muted d-block">Tampilkan di beranda</small>
                                                </label>
                                            </div>
                                            @error('is_featured')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Max Participants -->
                                        <div class="form-group">
                                            <label for="max_participants">Maks. Peserta</label>
                                            <input type="number"
                                                   class="form-control @error('max_participants') is-invalid @enderror"
                                                   id="max_participants"
                                                   name="max_participants"
                                                   value="{{ old('max_participants', $activity->max_participants) }}"
                                                   min="1"
                                                   placeholder="Kosongkan jika tidak terbatas">
                                            @error('max_participants')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Current Participants -->
                                        <div class="form-group">
                                            <label for="current_participants">Peserta Saat Ini</label>
                                            <input type="number"
                                                   class="form-control @error('current_participants') is-invalid @enderror"
                                                   id="current_participants"
                                                   name="current_participants"
                                                   value="{{ old('current_participants', $activity->current_participants) }}"
                                                   min="0">
                                            @error('current_participants')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Participation Status -->
                                        <div class="form-group">
                                            <label for="participation_status">Status Pendaftaran</label>
                                            <select class="form-control @error('participation_status') is-invalid @enderror"
                                                    id="participation_status"
                                                    name="participation_status">
                                                <option value="">-- Pilih Status --</option>
                                                <option value="open" {{ old('participation_status', $activity->participation_status) == 'open' ? 'selected' : '' }}>Terbuka</option>
                                                <option value="closed" {{ old('participation_status', $activity->participation_status) == 'closed' ? 'selected' : '' }}>Ditutup</option>
                                                <option value="full" {{ old('participation_status', $activity->participation_status) == 'full' ? 'selected' : '' }}>Penuh</option>
                                                <option value="by_invitation" {{ old('participation_status', $activity->participation_status) == 'by_invitation' ? 'selected' : '' }}>Undangan</option>
                                            </select>
                                            @error('participation_status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Info -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Koordinator</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Contact Person -->
                                        <div class="form-group">
                                            <label for="contact_person">Nama Koordinator</label>
                                            <input type="text"
                                                   class="form-control @error('contact_person') is-invalid @enderror"
                                                   id="contact_person"
                                                   name="contact_person"
                                                   value="{{ old('contact_person', $activity->contact_person) }}">
                                            @error('contact_person')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Contact Phone -->
                                        <div class="form-group">
                                            <label for="contact_phone">No. Telepon</label>
                                            <input type="text"
                                                   class="form-control @error('contact_phone') is-invalid @enderror"
                                                   id="contact_phone"
                                                   name="contact_phone"
                                                   value="{{ old('contact_phone', $activity->contact_phone) }}"
                                                   placeholder="Contoh: 08123456789">
                                            @error('contact_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Image Upload -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Gambar Kegiatan</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($activity->image)
                                            <div class="mb-3">
                                                <img src="{{ Storage::url($activity->image) }}"
                                                     alt="Current Image"
                                                     class="img-fluid rounded"
                                                     style="max-height: 200px;">
                                                <p class="text-muted mt-2 mb-0">Gambar saat ini</p>
                                            </div>
                                        @endif

                                        <div class="form-group">
                                            <label for="image">Upload Gambar Baru</label>
                                            <input type="file"
                                                   class="form-control-file @error('image') is-invalid @enderror"
                                                   id="image"
                                                   name="image"
                                                   accept="image/*">
                                            <small class="form-text text-muted">
                                                Format: JPG, PNG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengganti gambar.
                                            </small>
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('admin.activities.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Batal
                                            </a>
                                            <div>
                                                <button type="submit" name="save_action" value="save" class="btn btn-primary mr-2">
                                                    <i class="fas fa-save"></i> Simpan Perubahan
                                                </button>
                                                <button type="submit" name="save_action" value="save_and_continue" class="btn btn-success">
                                                    <i class="fas fa-save"></i> Simpan & Lanjut Edit
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill end date if start date is selected
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    startDate.addEventListener('change', function() {
        if (!endDate.value) {
            endDate.value = this.value;
        }
    });

    // Auto-calculate current participants based on max participants
    const maxParticipants = document.getElementById('max_participants');
    const currentParticipants = document.getElementById('current_participants');
    const participationStatus = document.getElementById('participation_status');

    function updateParticipationStatus() {
        const max = parseInt(maxParticipants.value) || 0;
        const current = parseInt(currentParticipants.value) || 0;

        if (max > 0 && current >= max) {
            participationStatus.value = 'full';
        } else if (current > 0) {
            participationStatus.value = 'open';
        }
    }

    maxParticipants.addEventListener('change', updateParticipationStatus);
    currentParticipants.addEventListener('change', updateParticipationStatus);
});
</script>
@endsection
