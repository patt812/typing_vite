<?php

namespace App\Providers;

use Faker\Generator as FakerGenerator;
use Illuminate\Support\ServiceProvider;

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
