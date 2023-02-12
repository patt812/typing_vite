<?php

namespace Database\Factories;

use App\Models\Sentence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sentence>
 */
class SentenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sentence::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'sentence' => $this->faker->sentence($this->faker),
            'kana' => $this->faker->kanaSentence($this->faker),
            'is_selected' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
