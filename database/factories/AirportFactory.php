<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AirportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $airports = ['G50', 'V35', 'CC65', 'LAC55', 'VT500', 'SC30'];
        $cities = ['Damascus', 'Arbil', 'Alriad', 'Birut', 'Brlen'];
        $countries = ['Syria', 'Iraq', 'Saudia', 'Lebanon', 'Germany'];
        return [
            'airport_name' => fake()->randomElement($airports),
            'city' => fake()->randomElement($cities),
            'country' => fake()->randomElement($countries),
        ];
    }
}
