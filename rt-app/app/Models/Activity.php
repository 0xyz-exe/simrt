<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'type',
        'status',
        'max_participants',
        'current_participants',
        'requirements',
        'contact_person',
        'contact_phone',
        'is_active',
        'is_featured'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
        'is_featured' => 'boolean'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString())
                    ->where('status', '!=', 'completed')
                    ->where('status', '!=', 'cancelled');
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getFormattedDateAttribute()
    {
        if ($this->end_date && $this->start_date != $this->end_date) {
            return $this->start_date->format('d M Y') . ' - ' . $this->end_date->format('d M Y');
        }
        return $this->start_date->format('d M Y');
    }

    public function getFormattedTimeAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return Carbon::parse($this->start_time)->format('H:i') . ' - ' .
                   Carbon::parse($this->end_time)->format('H:i');
        } elseif ($this->start_time) {
            return Carbon::parse($this->start_time)->format('H:i') . ' WIB';
        }
        return 'Waktu akan diumumkan';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'bg-gray-100 text-gray-800',
            'published' => 'bg-green-100 text-green-800',
            'scheduled' => 'bg-blue-100 text-blue-800',
            'ongoing' => 'bg-green-100 text-green-800',
            'completed' => 'bg-gray-100 text-gray-800',
            'cancelled' => 'bg-red-100 text-red-800'
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'draft' => 'Draft',
            'published' => 'Dipublikasi',
            'scheduled' => 'Dijadwalkan',
            'ongoing' => 'Sedang Berlangsung',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }

    public function getTypeTextAttribute()
    {
        $types = [
            'social' => 'Sosial',
            'religious' => 'Keagamaan',
            'community' => 'Komunitas',
            'sports' => 'Olahraga',
            'education' => 'Edukasi',
            'health' => 'Kesehatan',
            'environment' => 'Lingkungan',
            'meeting' => 'Rapat',
            'other' => 'Lainnya',
            // Legacy types for backward compatibility
            'gotong_royong' => 'Gotong Royong',
            'rapat' => 'Rapat RT',
            'acara_khusus' => 'Acara Khusus',
            'olahraga' => 'Olahraga',
            'sosial' => 'Kegiatan Sosial',
            'keagamaan' => 'Kegiatan Keagamaan',
            'pendidikan' => 'Pendidikan',
            'kesehatan' => 'Kesehatan'
        ];

        return $types[$this->type] ?? 'Lainnya';
    }

    public function getParticipationStatusAttribute()
    {
        if (!$this->max_participants) return null;

        $percentage = ($this->current_participants / $this->max_participants) * 100;

        if ($percentage >= 80) return 'Hampir Penuh';
        if ($percentage >= 50) return 'Setengah Penuh';
        if ($percentage > 0) return 'Tersedia';

        return 'Kosong';
    }
}
