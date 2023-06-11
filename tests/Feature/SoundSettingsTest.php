<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoundSettingsTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_set_sound()
    {
        $setting = $this->user->settings;
        $setting_play = $setting->settingPlays;

        $data = [
            'use_type_sound' => true,
            'use_beep_sound' => true,
            'volume' => 0.5,
        ];

        $response = $this->put(route('settings.sound', $setting_play), $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('setting_plays', [
            'id' => $setting_play->id,
            'setting_id' => $setting->id,
            'use_type_sound' => 1,
            'use_beep_sound' => 1,
            'volume' => 0.5,
        ]);
    }
}
