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
            'date_of_issue' => $this->faker->dateTime($max = 'now', $timezone = null),
            'status' => $this->faker->randomElement($array = array ('Nowe','Potwierdzone','Zafakturowane'))
        ];
    }
}
