<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function settings()
    {
        return $this->hasOne(Setting::class);
    }

    public function sentences(): HasMany
    {
        return $this->hasMany(Sentence::class);
    }

    /**
     * Play用の文章を取得する
     *
     * @return Collection
     */
    public function prepareSentences()
    {
        $user = Auth::user();
        $settings_limit = $user->settings->setting_preferences->sentences;
        $is_random = $user->settings->setting_preferences->is_random;

        $query = $this->sentences();
        if ($is_random) {
            $query->inRandomOrder();
        }

        $sentences = $query->where('user_id', $user->id)->where('is_selected', 1)
            ->limit($settings_limit)->get();

        // ヒットが出題数より足りない場合は補填
        while (count($sentences) < $settings_limit) {
            $sentences[] = $sentences[rand(0, (count($sentences) - 1))];
        }

        return $sentences;
    }
}
