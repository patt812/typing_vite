<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SentencePreferenceTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_store_preference()
    {
        $data = [
            'sentences' => 10,
            'prior_no_stats' => true,
            'is_random' => true,
            'min_accuracy' => 90,
            'max_accuracy' => 100,
            'min_wpm' => 50,
            'max_wpm' => 120,
            'limit_wpm' => true,
            'limit_accuracy' => true,
        ];

        $response = $this->put(route('preference.store'), $data);

        $response->assertStatus(200);
        $response->assertSessionHas('message', '更新しました。');

        $this->assertDatabaseHas('setting_preferences', [
            'setting_id' => $this->user->settings->id,
            'sentences' => 10,
            'prior_no_stats' => true,
            'is_random' => true,
            'min_accuracy' => 90,
            'max_accuracy' => 100,
            'min_wpm' => 50,
            'max_wpm' => 120,
            'limit_wpm' => true,
            'limit_accuracy' => true,
        ]);
    }

    public function test_store_preference_validation_error_required_fields()
    {
        $data = [
            'sentences' => '',
            'prior_no_stats' => '',
            'is_random' => '',
        ];

        $response = $this->put(route('preference.store'), $data);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['sentences', 'prior_no_stats', 'is_random']);
    }

    public function test_store_preference_validation_error_invalid_values()
    {
        $data = [
            'sentences' => -5,
            'prior_no_stats' => true,
            'is_random' => 'invalid',
            'min_accuracy' => 'string',
            'max_accuracy' => -10,
            'min_wpm' => 'another_string',
            'max_wpm' => -100,
            'limit_wpm' => true,
            'limit_accuracy' => true,
        ];

        $response = $this->put(route('preference.store'), $data);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'sentences',
            'is_random',
            'min_accuracy',
            'max_accuracy',
            'min_wpm',
            'max_wpm',
        ]);
    }

    public function test_store_sentence_preference_success()
    {
        $sentences = $this->user->sentences;

        $payload = [
            'sentence' => $sentences->map(function ($sentence, $index) {
                return [
                    'id' => $sentence->id,
                    'is_selected' => $index % 2 === 0,
                ];
            })->toArray(),
        ];

        $this->put(route('preference.sentence.store'), $payload)
            ->assertStatus(200);

        foreach ($sentences as $index => $sentence) {
            $this->assertDatabaseHas('sentences', [
                'id' => $sentence->id,
                'is_selected' => $index % 2 === 0,
            ]);
        }
    }

    public function test_store_sentence_preference_invalid_user_id()
    {
        $anotherUser = User::factory()->create();
        $sentences = $anotherUser->sentences;

        $payload = [
            'sentence' => $sentences->map(function ($sentence, $index) {
                return [
                    'id' => $sentence->id,
                    'is_selected' => $index % 2 === 0,
                ];
            })->toArray(),
        ];

        $this->put(route('preference.sentence.store'), $payload)
            ->assertStatus(200);

        foreach ($sentences as $sentence) {
            $this->assertDatabaseHas('sentences', [
                'id' => $sentence->id,
                'is_selected' => $sentence->is_selected,
            ]);
        }
    }
}
