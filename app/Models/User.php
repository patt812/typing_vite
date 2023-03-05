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

class User extends Authenticatable
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

    public static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->total_stats()->create([
                'user_id' => $user->id,
            ]);
            $settings = $user->settings()->create([
                'user_id' => $user->id,
            ]);
            $user->settings->setting_preferences()->create([
                'setting_id' => $settings->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $user->settings->setting_plays()->create([
                'setting_id' => $settings->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Sentence::factory(5)->create(['user_id' => $user->id]);
        });
    }

    public function settings()
    {
        return $this->hasOne(Setting::class);
    }

    public function sentences(): HasMany
    {
        return $this->hasMany(Sentence::class);
    }

    public function total_stats()
    {
        return $this->hasOne(UserStat::class);
    }

    /**
     * Play用の文章を取得する
     *
     * @return Collection
     */
    public function prepareSentences()
    {
        $user = Auth::user();
        $settings = $user->settings->setting_preferences;

        $query = $this->sentences()->leftJoin('sentence_stats as stats', 'sentences.id', 'sentence_id')
                ->where('user_id', $user->id);

        // 統計のない文章を出題するか
        if ($settings->prior_no_stats < 2) {
            $query->whereNull('stats.id');
        }
        if ($settings->prior_no_stats > 0) {
            $query->where('is_selected', 1);
        }

        // 統計の情報で制限するか（統計のない文章を優先する場合は未出題のヒット数が規定の出題数を超えていないこと）
        if (($settings->prior_no_stats == 2 || ($settings->prior_no_stats < 2 && $query->count() < $settings->sentences))) {
            $query->orWhere(function ($sub) use ($settings) {
                if ($settings->limit_wpm) {
                    if ($settings->min_wpm != null) {
                        $sub->where('ave_wpm', '>=', $settings->min_wpm);
                    }
                    if ($settings->max_wpm != null) {
                        $sub->where('ave_wpm', '<=', $settings->max_wpm);
                    }
                }
                if ($settings->limit_accuracy) {
                    if ($settings->min_accuracy != null) {
                        $sub->where('ave_accuracy', '>=', $settings->min_accuracy);
                    }
                    if ($settings->max_accuracy != null) {
                        $sub->where('ave_accuracy', '<=', $settings->max_accuracy);
                    }
                }
                if ($settings->prior_no_stats > 0) {
                    $sub->where('is_selected', 1);
                }
            });
        }

        if ($settings->is_random) {
            $query->inRandomOrder();
        }
        $sentences = $query->select('stats.*', 'sentences.*')->limit($settings->sentences)->get();

        // ヒットが出題数より足りない場合は補填
        if (!count($sentences)) {
            $query = $this->sentences()->where('user_id', $user->id);
            if ($settings->is_random) {
                $query->inRandomOrder();
            }
            $can_select = $query->where('is_selected', 1)->count();
            if ($can_select) {
                $query->where('is_selected', 1);
            }
            $sentences = $query->select('stats.*', 'sentences.*')->limit($settings->sentences)->get();
        }
        while (count($sentences) < $settings->sentences) {
            $sentences[] = $sentences[rand(0, (count($sentences) - 1))];
        }

        return $sentences;
    }
}
