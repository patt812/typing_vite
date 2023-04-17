<?php

namespace Database\Factories;

use App\Models\Stats;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stats>
 */
class StatsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stats::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'sentence_id' => null,
            'played' => $this->faker->numberBetween(0, 100),
            'min_wpm' => $this->faker->randomFloat(2, 10, 100),
            'max_wpm' => $this->faker->randomFloat(2, 10, 100),
            'ave_wpm' => $this->faker->randomFloat(2, 10, 100),
            'min_accuracy' => $this->faker->randomFloat(2, 0, 100),
            'max_accuracy' => $this->faker->randomFloat(2, 0, 100),
            'ave_accuracy' => $this->faker->randomFloat(2, 0, 100),
            'perfect' => $this->faker->numberBetween(0, 100),
            'max_miss_streak' => $this->faker->numberBetween(0, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
