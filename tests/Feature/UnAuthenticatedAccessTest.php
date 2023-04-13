<?php

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnAuthenticatedAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未認証ユーザーがルートにアクセスできないことをテスト
     *
     * @return void
     */
    public function test_cannot_access_root()
    {
        $response = $this->get(RouteServiceProvider::HOME);

        $response->assertRedirect(route('login'));
    }

    /**
     * 未認証ユーザーが出題設定画面にアクセスできないことをテスト
     *
     * @return void
     */
    public function test_cannot_access_preference()
    {
        $response = $this->get(route('preference'));

        $response->assertRedirect(route('login'));
    }

    /**
     * 未認証ユーザーが文章登録画面にアクセスできないことをテスト
     *
     * @return void
     */
    public function test_cannot_access_sentence()
    {
        $response = $this->get(route('sentence'));

        $response->assertRedirect(route('login'));
    }

    /**
     * 未認証ユーザーが統計管理画面にアクセスできないことをテスト
     *
     * @return void
     */
    public function test_cannot_access_statistics()
    {
        $response = $this->get(route('stats'));

        $response->assertRedirect(route('login'));
    }

    /**
     * 未認証ユーザーがユーザー設定画面にアクセスできないことをテスト
     *
     * @return void
     */
    public function test_cannot_access_user_setting_edit()
    {
        $response = $this->get(route('profile.show'));

        $response->assertRedirect(route('login'));
    }
}
