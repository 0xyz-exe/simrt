<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Due extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'amount',
        'status',
        'household_id'
    ];

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
