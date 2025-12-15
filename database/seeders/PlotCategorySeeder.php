<?php

namespace Database\Seeders;

use App\Models\PlotCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlotCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $marla_size = [3, 5, 6, 7, 8, 10, 12, 15, 18];

        foreach ($marla_size as $size) {
            PlotCategory::create([
                'name' =>  $size.' marla'
            ]);
        };

    }
}
