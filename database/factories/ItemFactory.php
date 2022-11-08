<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => join(' ', fake()->unique()->words(2)),
            'price' => fake()->randomFloat(2, 500, 20000),
            'image_url' => "https://picsum.photos/seed/" . fake()->word . "/200",
            'code' => explode('-', fake()->uuid())[0],
        ];
    }
}
