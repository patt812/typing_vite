<?php

namespace Tests\Feature;

use App\Models\Sentence;
use App\Models\Stats;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsResetTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_reset_stat_for_single_sentence()
    {
        $sentence = Sentence::where('user_id', $this->user->id)->first();
        $stat = Stats::factory()->create(['sentence_id' => $sentence->id]);

        $response = $this->delete(route('stats.reset', $sentence->id), ['delete_sentence' => false]);

        $response->assertStatus(200);
        $response->assertSessionHas('message', '削除しました。');
        $this->assertDatabaseMissing('sentence_stats', ['id' => $stat->id]);
    }

    public function test_can_reset_stat_and_delete_single_sentence()
    {
        $sentence = Sentence::where('user_id', $this->user->id)->first();
        $stat = Stats::factory()->create(['sentence_id' => $sentence->id]);

        $response = $this->delete(route('stats.reset', $sentence->id), ['delete_sentence' => true]);

        $response->assertStatus(200);
        $response->assertSessionHas('message', '削除しました。');
        $this->assertDatabaseMissing('sentence_stats', ['id' => $stat->id]);
        $this->assertDatabaseMissing('sentences', ['id' => $sentence->id]);
    }

    public function test_can_reset_all_stats_for_user()
    {
        $sentences = Sentence::where('user_id', $this->user->id)->get();
        $stats = $sentences->each(fn ($sentence) => Stats::factory()->create([
            'sentence_id' => $sentence->id,
        ]));

        $response = $this->delete(route('stats.reset.all'));

        $response->assertStatus(200);
        $response->assertSessionHas('message', '削除しました。');
        $stats->each(fn ($stat) => $this->assertDatabaseMissing('sentence_stats', ['id' => $stat->id]));
    }
}
