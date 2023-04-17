<?php

namespace Tests\Feature;

use App\Models\Sentence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SentenceUpdateTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private Sentence $sentence;

    protected function setUp() : void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->sentence = Sentence::create([
            'user_id' => $this->user->id,
            'sentence' => 'これは変更前のテスト文です',
            'kana' => 'これはへんこうまえのてすとぶんです',
        ]);
    }

    public function test_can_update_sentence()
    {
        $request = new Request([
            'id' => $this->sentence->id,
            'sentence' => 'これは更新後のテスト文です',
            'kana' => 'これはこうしんごのてすとぶんです',
        ]);

        $response = $this->put(route('sentence.update', $this->sentence->id), $request->all());

        $response->assertStatus(200);
        $response->assertSessionHas('message', '更新しました。');

        $this->assertDatabaseHas('sentences', [
            'id' => $this->sentence->id,
            'user_id' => $this->user->id,
            'sentence' => $request->input('sentence'),
            'kana' => $request->input('kana'),
        ]);

        $this->assertDatabaseMissing('sentences', [
            'id' => $this->sentence->id,
            'sentence' => $this->sentence->sentence,
            'kana' => $this->sentence->kana,
        ]);
    }

    public function test_empty_sentence_or_kana_fails_validation()
    {
        $emptySentence = [
            'sentence' => '',
            'kana' => 'これはこうしんごのてすとぶんです',
        ];

        $response = $this->put(route('sentence.update', $this->sentence->id), $emptySentence);
        $response->assertSessionHasErrors(['sentence']);

        $emptyKana = [
            'sentence' => 'これは更新後のテスト文です',
            'kana' => '',
        ];

        $response = $this->put(route('sentence.update', $this->sentence->id), $emptyKana);
        $response->assertSessionHasErrors(['kana']);
    }

    public function test_sentence_or_kana_exceeding_max_length_fails_validation()
    {
        $longSentence = [
            'sentence' => str_repeat('あ', 256),
            'kana' => 'これはこうしんごのてすとぶんです',
        ];

        $response = $this->put(route('sentence.update', $this->sentence->id), $longSentence);
        $response->assertSessionHasErrors(['sentence']);

        $longKana = [
            'sentence' => 'これは更新後のテスト文です',
            'kana' => str_repeat('あ', 256),
        ];

        $response = $this->put(route('sentence.update', $this->sentence->id), $longKana);
        $response->assertSessionHasErrors(['kana']);
    }

    public function test_invalid_kana_fails_validation()
    {
        $invalidKana = [
            'sentence' => 'これは更新後のテスト文です',
            'kana' => 'これはInvalidKanaです',
        ];

        $response = $this->put(route('sentence.update', $this->sentence->id), $invalidKana);
        $response->assertSessionHasErrors(['kana']);
    }

    public function test_attempt_to_update_another_users_sentence_fails()
    {
        $anotherUser = User::factory()->create();
        $anotherUsersSentence = Sentence::create([
            'user_id' => $anotherUser->id,
            'sentence' => 'これは他のユーザーのテスト文です',
            'kana' => 'これはほかのゆーざーのてすとぶんです',
        ]);

        $data = [
            'sentence' => 'これは更新後のテスト文です',
            'kana' => 'これはこうしんごのてすとぶんです',
        ];

        $response = $this->put(route('sentence.update', ['id' => $anotherUsersSentence->id]), $data);
        $response->assertStatus(403);
    }

    public function test_can_delete_sentence()
    {
        $response = $this->delete(route('sentence.delete', ['id' => $this->sentence->id]));

        $response->assertStatus(200);
        $response->assertSessionHas('message', '削除しました。');

        $this->assertDatabaseMissing('sentences', [
            'id' => $this->sentence->id,
            'sentence' => $this->sentence->sentence,
            'kana' => $this->sentence->kana,
        ]);
    }

    public function test_attempt_to_delete_another_users_sentence_fails()
    {
        $anotherUser = User::factory()->create();
        $anotherUsersSentence = Sentence::create([
            'user_id' => $anotherUser->id,
            'sentence' => 'これは他のユーザーのテスト文です',
            'kana' => 'これはほかのゆーざーのてすとぶんです',
        ]);

        $response = $this->delete(route('sentence.delete', ['id' => $anotherUsersSentence->id]));
        $response->assertStatus(403);
    }
}
