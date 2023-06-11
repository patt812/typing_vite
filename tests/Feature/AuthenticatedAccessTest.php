<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AuthenticatedAccessTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * 認証済みユーザーがルートにアクセスできることをテスト
     *
     * @return void
     */
    public function test_can_access_and_render_dashboard_page_with_authenticated()
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Dashboard')
                ->has('sentences'),
        );
    }

    /**
     * 認証済みユーザーが出題設定画面にアクセスできることをテスト
     *
     * @return void
     */
    public function test_can_access_and_render_preference_show_page_with_authenticated()
    {
        $response = $this->get(route('preference'));

        $response->assertStatus(200);
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Preference/ShowPreference')
                ->has('sentences'),
        );
    }

    /**
     * 認証済みユーザーが文章登録画面にアクセスできることをテスト
     *
     * @return void
     */
    public function test_can_access_and_render_sentence_page_with_authenticated()
    {
        $response = $this->get(route('sentence'));

        $response->assertStatus(200);
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Sentence/ShowSentence')
                ->has('sentences'),
        );
    }

    /**
     * 認証済みユーザーが統計管理画面にアクセスできることをテスト
     *
     * @return void
     */
    public function test_can_access_and_render_stats_show_page_with_authenticated()
    {
        $response = $this->get(route('stats'));

        $response->assertStatus(200);
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Stats/ShowStats')
                ->has('sentences')
                ->has('userStats'),
        );
    }

    /**
     * 認証済みユーザーがユーザー設定画面にアクセスできることをテスト
     *
     * @return void
     */
    public function test_can_access_user_setting()
    {
        $response = $this->get(route('profile.show'));

        $response->assertStatus(200);
    }
}
