<?php

namespace App\Providers;

use Faker\Generator as FakerGenerator;
use Faker\Provider\Base as BaseProvider;
use Illuminate\Support\ServiceProvider;

class CustomFakerProvider extends BaseProvider
{
    // TODO データを変える
    protected $sentences = [
        "あ",
        "い",
        "う",
        "え",
        "お",
    ];
    protected $kanas = [
        "あ",
        "い",
        "う",
        "え",
        "お",
    ];

    protected $sentenceCounter = 0;
    protected $kanaCounter = 0;

    public function sentence(FakerGenerator $faker)
    {
        if ($this->sentenceCounter >= count($this->sentences)) {
            return null;
        }
        return $this->sentences[$this->sentenceCounter++];
    }

    public function kanaSentence(FakerGenerator $faker)
    {
        if ($this->kanaCounter >= count($this->kanas)) {
            return null;
        }
        return $this->kanas[$this->kanaCounter++];
    }
}

class CustomFakerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(FakerGenerator::class, function () {
            $faker = \Faker\Factory::create();
            $faker->addProvider(new CustomFakerProvider($faker));
            return $faker;
        });
    }
}
