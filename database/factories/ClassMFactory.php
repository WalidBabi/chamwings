<?php

namespace Database\Factories;

use App\Models\Airplane;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ClassMFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $class_name = ['Business', 'Economy', 'e.g'];
        $price_rate = [250, 700 ,850, 1000];
        $weight_allowed = [50, 70 ,60, 100050];
        $number_of_meals = [1,2,3,4,5,6,7,8,9];
        $number_of_seats = [1,2,3,4,5,6,7,8,9];
        $airplanes_id = [1,2,3,4,5,6,7,8,9,10];
        
        return [
            'airplane_id'=>fake()->randomElement($airplanes_id),
            'class_name'=>fake()->randomElement($class_name),
            'price_rate'=>fake()->randomElement($price_rate),
            'weight_allowed'=>fake()->randomElement($weight_allowed),
            'number_of_meals'=>fake()->randomElement($number_of_meals),
            'number_of_seats'=>fake()->randomElement($number_of_seats),
        ];
    }
}