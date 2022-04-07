<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'filename' => Str::random(10).'.xml',
            'order_number' => $this->faker->unique()->numberBetween($min = 1000, $max = 9000).'-order' ,
            'order_date' => $this->faker->dateTime($max = 'now', $timezone = null),
            'expected_delivery_date' => $this->faker->dateTime($max = 'now', $timezone = null),
            'document_function_code' => $this->faker->numberBetween($min = 1, $max = 1000),
            'buyer_iln' => $this->faker->numberBetween($min = 0000000000000, $max = 9999999999999),
            'seller_iln' => $this->faker->numberBetween($min = 0000000000000, $max = 9999999999999),
            'delivery_point_iln' => $this->faker->numberBetween($min = 0000000000000, $max = 9999999999999),
            'date_of_issue' => $this->faker->dateTime($max = 'now', $timezone = null),
            'status' => $this->faker->randomElement($array = array ('Nowe','Potwierdzone','Zafakturowane'))
        ];
    }
}