<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities_name = ['Aleppo', 'Damascus', 'Daraa', 'Deir ez-Zor', 'Hama', 'Homs', 'Idlib', 'Quneitra', 'Ar-Raqqah', 'As-Suwayda', 'Tartus'];
        $cities_code = ['ALE', 'DAM', 'DAR', 'DEZ', 'HAM', 'HOM', 'IDL', 'QUN', 'RAQ', 'SWY', 'TAR'];

        for ($i = 0; $i <11 ; $i++) {
            City::create([
                'code' => $cities_code[$i],
                'city_name' => $cities_name[$i],
            ]);
        }
    }
}