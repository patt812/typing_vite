<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingPreference extends Model
{
    use HasFactory;

    protected $fillable = ['setting_id', 'is_random', 'sentences'];

    protected $casts = [
        'is_random' => 'boolean',
    ];

    public function settings()
    {
        return $this->belongsTo(Settings::class, 'setting_id', 'id');
    }
}
