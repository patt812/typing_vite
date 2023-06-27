<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class GuestAuthenticationTest extends TestCase
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

    public function test_guest_can_login_and_session_is_updated()
    {
        $response = $this->postJson(route('guest.login'));

        $response->assertStatus(302);
        $response->assertRedirect(route('guest.dashboard'));

        $response->assertSessionHas('guest_data');
        $sessionData = Session::get('guest_data');

        $this->assertIsArray($sessionData);
        $this->assertArrayHasKey('sentence', $sessionData);
        $this->assertArrayHasKey('total_stats', $sessionData);

        $sentence_path = resource_path('sentence/guest-template.json');
        $sentences = json_decode(File::get($sentence_path), true);

        foreach ($sentences as $index => $sentence) {
            $this->assertEquals($index, $sessionData['sentence'][$index]['id']);
            $this->assertEquals($sentence['sentence'], $sessionData['sentence'][$index]['sentence']);
            $this->assertEquals($sentence['kana'], $sessionData['sentence'][$index]['kana']);
            $this->assertArrayHasKey('stat', $sessionData['sentence'][$index]);
            $this->assertEquals(0, $sessionData['sentence'][$index]['stat']['played']);
        }

        $this->assertEquals(0, $sessionData['total_stats']['played']);
        $this->assertEquals(0, $sessionData['total_stats']['typed']);
        $this->assertEquals(0, $sessionData['total_stats']['accuracy']);
        $this->assertEquals(0, $sessionData['total_stats']['wpm']);
        $this->assertEquals(0, $sessionData['total_stats']['max_wpm']);
        $this->assertEquals(0, $sessionData['total_stats']['played_seconds']);
    }

    public function test_guest_can_logout_and_session_is_cleared()
    {
        $sessionData = self::MOCKED_SESSION;
        session($sessionData);

        $response = $this->postJson(route('guest.logout'));

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));

        $this->assertFalse(Session::has('guest_data'));
    }
}
