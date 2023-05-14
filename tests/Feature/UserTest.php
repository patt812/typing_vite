<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_new_user_creation()
    {
        $this->assertDatabaseHas('users', [
            'name' => $this->user->name,
            'email' => $this->user->email,
        ]);

        $this->assertDatabaseHas('user_stats', [
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('settings', [
            'user_id' => $this->user->id,
        ]);

        $this->assertCount(env('DEFAULT_AUTOCOMPLETE_SENTENCES', 5), $this->user->sentences);
    }

    public function test_prepare_sentences()
    {
        $this->actingAs($this->user);
        $sentences = $this->user->prepareSentences();

        $this->assertCount(5, $sentences);

        $sentence_ids = $sentences->pluck('id')->toArray();
        $user_sentence_ids = $this->user->sentences->pluck('id')->toArray();

        foreach ($sentence_ids as $id) {
            $this->assertContains($id, $user_sentence_ids);
        }
    }
}
