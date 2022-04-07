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
        $quantity=$this->faker->numberBetween($min = 1, $max = 10);
        return [
            // 'lp' => $this->faker->unique()->numberBetween($min = 1000, $max = 9000),
            // 'product' => $this->faker->unique()->sentence($nbWords = 4, $variableNbWords = true),
            // 'jm' => $this->faker->randomElement($array = array ('OP','SZT')),
            // 'quantity' => $quantity,
            // 'quantityUpdated' => $quantity

            
            'line_number' => $this->faker->unique()->numberBetween($min = 1000, $max = 9000),
            'ean' => $this->faker->unique()->numberBetween($min = 1000, $max = 9000),
            'buyer_item_code' => $this->faker->unique()->numberBetween($min = 1000, $max = 9000),
            'item_description' => $this->faker->unique()->sentence($nbWords = 4, $variableNbWords = true),
            // 'product' => $this->faker->unique()->sentence($nbWords = 4, $variableNbWords = true),
            // 'product' => $this->faker->unique()->sentence($nbWords = 4, $variableNbWords = true),
            'unit_of_measure' => $this->faker->randomElement($array = array ('OP','SZT')),
            'expected_delivery_date' => $this->faker->dateTime($max = 'now', $timezone = null),
            'ordered_quantity' => $quantity,
            'ordered_quantity_updated' => $quantity

        ];
    }
}
