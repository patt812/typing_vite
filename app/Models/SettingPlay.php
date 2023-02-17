<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingPlay extends Model
{
    use HasFactory;

    protected $fillable = ['setting_id', 'use_type_sound', 'use_beep_sound', 'volume'];

    protected $casts = [
        'use_type_sound' => 'boolean',
        'use_beep_sound' => 'boolean',
    ];

    public function settings()
    {
        return $this->belongsTo(Settings::class, 'setting_id', 'id');
    }
}
