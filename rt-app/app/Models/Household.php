<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_kk',
        'alamat',
        'rt',
        'rw',
        'kepala_keluarga_id'
    ];

    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    public function kepalaKeluarga()
    {
        return $this->belongsTo(Resident::class, 'kepala_keluarga_id');
    }

    public function dues()
    {
        return $this->hasMany(Due::class);
    }
}
