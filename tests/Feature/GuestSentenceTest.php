<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class GuestSentenceTest extends TestCase
{
    private const MOCKED_SESSION = [
        'guest_data' => [
            'sentence' => [
                [
                    'id' => 0,
                    'sentence' => 'テスト',
                    'kana' => 'てすと',
                    'stat' => [
                        "played" => 0,
                        "min_wpm" => null,
                        "max_wpm" => null,
                        "ave_wpm" => null,
                        "min_accuracy" => null,
                        "max_accuracy" => null,
                        "ave_accuracy" => null,
                        "perfect" => 0,
                        "max_miss_streak" => 0,
                    ],
                ],
            ],
            'total_stats' => [
                'played' => 0,
                'typed' => 0,
                'accuracy' => 0,
                'wpm' => 0,
                'max_wpm' => 0,
                'played_seconds' => 0,
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Session::put(self::MOCKED_SESSION);
    }

    public function test_can_store_sentence()
    {
        $newSentenceData = [
            'sentence' => '新しいテスト',
            'kana' => 'あたらしいてすと',
        ];

        $response = $this->putJson(route('guest.sentence.store'), $newSentenceData);

        $response->assertStatus(200);

        $guestData = Session::get('guest_data');
        $this->assertCount(2, $guestData['sentence']);
        $this->assertEquals($newSentenceData['sentence'], end($guestData['sentence'])['sentence']);
    }

    public function test_cannot_store_more_than_ten_sentences()
    {
        $sessionData = self::MOCKED_SESSION;
        for ($i = 1; $i < 10; $i++) {
            $sessionData['guest_data']['sentence'][] = [
                'id' => $i,
                'sentence' => "テスト{$i}",
                'kana' => "てすと{$i}",
                'stat' => self::MOCKED_SESSION['guest_data']['sentence'][0]['stat'],
            ];
        }
        Session::put($sessionData);

        $newSentenceData = [
            'sentence' => '新しいテスト',
            'kana' => 'あたらしいてすと',
        ];

        $response = $this->putJson(route('guest.sentence.store'), $newSentenceData);

        $response->assertSessionHas('message', '10件以上登録できません。');

        $guestData = Session::get('guest_data');
        $this->assertCount(10, $guestData['sentence']);
    }

    public function test_can_update_sentence()
    {
        $sentenceIndex = 0;
        $updatedSentenceData = [
            'index' => $sentenceIndex,
            'sentence' => '更新したテスト',
            'kana' => 'こうしんしたてすと',
        ];

        $response = $this->putJson(route('guest.sentence.update'), $updatedSentenceData);
        $response->assertStatus(200);

        $guestData = Session::get('guest_data');
        $this->assertEquals(
            $updatedSentenceData['sentence'],
            $guestData['sentence'][$sentenceIndex]['sentence'],
        );

        $response->assertSessionHas('message', '更新しました。');
    }

    public function test_cannot_store_sentence_with_duplicate_kana()
    {
        $newSentenceData = [
            'sentence' => '新しいテスト',
            'kana' => 'てすと',
        ];

        $response = $this->putJson(route('guest.sentence.store'), $newSentenceData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('kana');

        $guestData = Session::get('guest_data');
        $this->assertCount(1, $guestData['sentence']);
    }

    public function test_cannot_update_sentence_with_duplicate_kana()
    {
        $newSentenceData = [
            'sentence' => '新しいテスト',
            'kana' => 'あたらしいてすと',
        ];
        $this->putJson(route('guest.sentence.store'), $newSentenceData);

        $updatedSentenceData = [
            'index' => 1,
            'sentence' => '更新したテスト',
            'kana' => 'てすと',
        ];

        $response = $this->putJson(route('guest.sentence.update'), $updatedSentenceData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('kana');

        $guestData = Session::get('guest_data');
        $this->assertNotEquals($updatedSentenceData['sentence'], $guestData['sentence'][1]['sentence']);
        $this->assertNotEquals($updatedSentenceData['kana'], $guestData['sentence'][1]['kana']);
    }

    public function test_can_delete_sentence()
    {
        $sentenceIndex = 0;

        $response = $this->deleteJson(route('guest.sentence.delete'), ['index' => $sentenceIndex]);

        $response->assertStatus(200);

        $guestData = Session::get('guest_data');
        $this->assertCount(0, $guestData['sentence']);
    }

    public function test_cannot_delete_sentence_with_invalid_index()
    {
        $invalidIndex = 999;

        $response = $this->deleteJson(route('guest.sentence.delete'), ['index' => $invalidIndex]);

        $response->assertStatus(200);

        $guestData = Session::get('guest_data');
        $this->assertCount(1, $guestData['sentence']);
    }

    public function test_can_reset_sentence_stat()
    {
        $sentenceIndex = 0;

        $response = $this->deleteJson(route('guest.stats.reset', ['sentence_id' => $sentenceIndex]));

        $response->assertStatus(200);

        $guestData = Session::get('guest_data');
        $this->assertEquals(self::MOCKED_SESSION['guest_data']['sentence'][0]['stat'], $guestData['sentence'][0]['stat']);
    }

    public function test_can_delete_sentence_with_delete_flag()
    {
        $sentenceIndex = 0;

        $response = $this->deleteJson(route('guest.stats.reset', ['sentence_id' => $sentenceIndex]), ['delete_sentence' => true]);

        $response->assertStatus(200);

        $guestData = Session::get('guest_data');
        $this->assertCount(0, $guestData['sentence']);
    }

    public function test_can_store_result()
    {
        $resultData = [
            'result' => [
                'ids' => [0],
                'avarages' => [120],
                'accuracies' => [90],
                'missStreaks' => [2],
            ],
            'stats' => [
                'totalCorrect' => 90,
                'totalMistake' => 10,
                'totalWPM' => 120,
                'time' => 60,
            ],
        ];

        $response = $this->postJson(route('guest.play.store'), $resultData);

        $response->assertStatus(200);

        $guestData = Session::get('guest_data');
        $this->assertEquals(1, $guestData['total_stats']['played']);
        $this->assertEquals(100, $guestData['total_stats']['typed']);
        $this->assertEquals(90, $guestData['total_stats']['accuracy']);
        $this->assertEquals(120, $guestData['total_stats']['wpm']);
        $this->assertEquals(120, $guestData['total_stats']['max_wpm']);
        $this->assertEquals(60, $guestData['total_stats']['played_seconds']);

        $this->assertEquals(1, $guestData['sentence'][0]['stat']['played']);
        $this->assertEquals(120, $guestData['sentence'][0]['stat']['min_wpm']);
        $this->assertEquals(120, $guestData['sentence'][0]['stat']['max_wpm']);
        $this->assertEquals(120, $guestData['sentence'][0]['stat']['ave_wpm']);
        $this->assertEquals(90, $guestData['sentence'][0]['stat']['min_accuracy']);
        $this->assertEquals(90, $guestData['sentence'][0]['stat']['max_accuracy']);
        $this->assertEquals(90, $guestData['sentence'][0]['stat']['ave_accuracy']);
        $this->assertEquals(0, $guestData['sentence'][0]['stat']['perfect']);
        $this->assertEquals(2, $guestData['sentence'][0]['stat']['max_miss_streak']);
    }
}
