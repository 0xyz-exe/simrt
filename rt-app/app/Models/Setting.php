<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'sort_order',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    // Helper methods
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getByGroup($group)
    {
        return static::where('group', $group)
            ->orderBy('sort_order')
            ->get()
            ->pluck('value', 'key');
    }

    public static function getPublicSettings()
    {
        return static::where('is_public', true)
            ->get()
            ->pluck('value', 'key');
    }

    // Accessors
    public function getDisplayValueAttribute()
    {
        switch ($this->type) {
            case 'boolean':
                return $this->value ? 'Ya' : 'Tidak';
            case 'image':
                return $this->value ? asset('storage/' . $this->value) : null;
            default:
                return $this->value;
        }
    }
}
