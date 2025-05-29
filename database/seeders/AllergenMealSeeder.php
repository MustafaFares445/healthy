<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AllergenMealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('allergens')->insert([
            ['id' => 1, 'name' => 'المكسرات'],
            ['id' => 2, 'name' => 'الغلوتين'],
            ['id' => 3, 'name' => 'الألبان'],
            ['id' => 4, 'name' => 'البيض'],
            ['id' => 5, 'name' => 'السمسم'],
        ]);
    }
}
