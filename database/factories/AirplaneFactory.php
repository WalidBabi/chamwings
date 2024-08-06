<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AirplaneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $model = ['MS60', 'OP1920', 'SCOP500', 'VP50', 'S350'];
        $manufacturer = ['manufacturer1', 'manufacturer2', 'manufacturer3', 'manufacturer4', 'manufacturer5'];
        $range = ['range1', 'range2', 'range3', 'range4', 'range5'];
        return [
            'model'=>fake()->randomElement($model),
            'manufacturer'=>fake()->randomElement($manufacturer),
            'range'=>fake()->randomElement($range),
        ];
    }
}