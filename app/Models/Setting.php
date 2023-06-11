<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function settingPreferences()
    {
        return $this->hasOne(SettingPreference::class, 'setting_id', 'id');
    }

    public function settingPlays()
    {
        return $this->hasOne(SettingPlay::class, 'setting_id', 'id');
    }
}
