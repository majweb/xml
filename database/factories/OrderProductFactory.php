<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderProduct>
 */
class OrderProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'lp' => $this->faker->unique()->numberBetween($min = 1000, $max = 9000),
            'product' => $this->faker->unique()->sentence($nbWords = 4, $variableNbWords = true),
            'jm' => $this->faker->randomElement($array = array ('OP','SZT')),
            'quantity' => $this->faker->numberBetween($min = 1, $max = 10)
        ];
    }
}
