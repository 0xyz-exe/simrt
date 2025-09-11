<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'due_id',
        'amount',
        'method',
        'paid_at',
        'notes'
    ];

    protected $casts = [
        'paid_at' => 'datetime'
    ];

    public function due()
    {
        return $this->belongsTo(Due::class);
    }
}
