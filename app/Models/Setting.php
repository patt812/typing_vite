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

    public function setting_preferences()
    {
        return $this->hasOne(SettingPreference::class, 'setting_id', 'id');
    }

    public function setting_plays()
    {
        return $this->hasOne(SettingPlay::class, 'setting_id', 'id');
    }
}
