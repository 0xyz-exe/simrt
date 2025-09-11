<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'owner_name',
        'category',
        'address',
        'phone',
        'whatsapp',
        'email',
        'instagram',
        'facebook',
        'website',
        'image',
        'operating_hours',
        'is_active',
        'featured',
        'resident_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'featured' => 'boolean',
        'operating_hours' => 'array'
    ];

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-business.jpg');
    }

    public function getFormattedPhoneAttribute()
    {
        return $this->phone ? preg_replace('/(\d{4})(\d{4})(\d{4})/', '$1-$2-$3', $this->phone) : null;
    }

    public function getWhatsappLinkAttribute()
    {
        if ($this->whatsapp) {
            $number = preg_replace('/[^0-9]/', '', $this->whatsapp);
            if (substr($number, 0, 1) === '0') {
                $number = '62' . substr($number, 1);
            }
            return "https://wa.me/{$number}";
        }
        return null;
    }

    public static function getCategories()
    {
        return [
            'kuliner' => 'Kuliner',
            'fashion' => 'Fashion',
            'elektronik' => 'Elektronik',
            'kerajinan' => 'Kerajinan',
            'jasa' => 'Jasa',
            'kesehatan' => 'Kesehatan & Kecantikan',
            'pendidikan' => 'Pendidikan',
            'otomotif' => 'Otomotif',
            'pertanian' => 'Pertanian',
            'lainnya' => 'Lainnya'
        ];
    }
}
