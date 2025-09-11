<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'nama',
        'phone',
        'email',
        'household_id',
        'is_head',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin'
    ];

    protected $casts = [
        'is_head' => 'boolean',
        'tanggal_lahir' => 'date'
    ];

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    public function letters()
    {
        return $this->hasMany(Letter::class);
    }
}
