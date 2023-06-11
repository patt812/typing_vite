<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SentenceRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_register_sentence()
    {
        $request = new Request([
            'sentence' => 'これはテスト文です。',
            'kana' => 'これはてすとぶんです。',
        ]);

        $response = $this->put(route('sentence.store'), $request->all());

        $response->assertStatus(200);
        $response->assertSessionHas('message', '登録しました。');

        $this->assertDatabaseHas('sentences', [
            'user_id' => $this->user->id,
            'sentence' => $request->input('sentence'),
            'kana' => $request->input('kana'),
        ]);
    }

    public function test_cannot_register_sentence_with_missing_parameters()
    {
        $request = new Request([
            'sentence' => '',
            'kana' => '',
        ]);

        $response = $this->put(route('sentence.store'), $request->all());

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['sentence', 'kana']);
    }

    public function test_cannot_register_sentence_with_exceeding_max_length()
    {
        $request = new Request([
            'sentence' => str_repeat('a', 256),
            'kana' => str_repeat('あ', 256),
        ]);

        $response = $this->put(route('sentence.store'), $request->all());

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['sentence', 'kana']);
    }

    public function test_cannot_register_sentence_with_invalid_kana()
    {
        $request = new Request([
            'sentence' => 'これはテスト文です。',
            'kana' => 'これは*てすとぶんです。',
        ]);

        $response = $this->put(route('sentence.store'), $request->all());

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['kana']);
    }

    public function test_can_register_multiple_sentences()
    {
        $sentences = [];
        for ($i = 0; $i < 100; $i++) {
            $sentences[] = [
                'sentence' => 'これはテスト文' . $i . 'です。',
                'kana' => 'これはてすとぶん' . $i . 'です。',
            ];
        }

        $response = $this->post(route('sentences.store'), ['sentences' => $sentences]);
        $response->assertStatus(200);
        $response->assertSessionHas('message', count($sentences) . '件の文章を登録しました。');

        foreach ($sentences as $sentence) {
            $this->assertDatabaseHas('sentences', [
                'user_id' => $this->user->id,
                'sentence' => $sentence['sentence'],
                'kana' => $sentence['kana'],
            ]);
        }
    }
}
