<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class GuestAccessTest extends TestCase
{
    use RefreshDatabase;

    private const SENTENCE = [
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
    ];

    private const MOCKED_SESSION = [
        'guest_data' => [
            'sentence' => [
                self::SENTENCE,
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

    private $mockedSession;

    protected function setUp(): void
    {
        parent::setUp();

        $guest_data = self::MOCKED_SESSION;

        for ($i = 1; $i < 4; $i++) {
            $guest_data['guest_data']['sentence'][] = [
                'id' => $i,
                'sentence' => 'テスト' . $i,
                'kana' => 'てすと' . $i,
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
            ];
        }

        $this->mockedSession = $guest_data;
        Session::put($guest_data);
    }

    public function test_show_sentence()
    {
        $response = $this->get(route('guest.sentence'));

        $response->assertStatus(200);

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Sentence/ShowSentence')
            ->has('sentences')
            ->where('sentences', $this->mockedSession['guest_data']['sentence']));
    }

    public function test_show_preference()
    {
        $response = $this->get(route('guest.preference'));

        $response->assertStatus(200);

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Preference/ShowPreference')
            ->has('sentences')
            ->where('sentences', $this->mockedSession['guest_data']['sentence']));
    }

    public function test_show_stats()
    {
        $response = $this->get(route('guest.stats'));

        $response->assertStatus(200);

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Stats/ShowStats')
            ->has('sentences')
            ->where('sentences', $this->mockedSession['guest_data']['sentence']));
    }

    public function test_show_dashboard()
    {
        $response = $this->get(route('guest.dashboard'));

        $response->assertStatus(200);

        $expectedSentences = array_slice($this->mockedSession['guest_data']['sentence'], 0, 5);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Dashboard')
            ->has('sentences', count($expectedSentences)));
    }

    public function test_redirect_to_guest_dashboard_when_access_login()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(302);

        $response->assertRedirect(route('guest.dashboard'));
    }

    public function test_user_cannot_access_guest_dashboard()
    {
        Session::flush();
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('guest.dashboard'));

        $response->assertStatus(302);

        $response->assertRedirect(route('dashboard'));
    }
}
