<?php

namespace Database\Seeders;

use App\Models\Plot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
     {

        for($number = 1 ; $number <= 147 ; $number++ ){


            if( $number == 1 ){

                Plot::create([
                    'category_id' => 7,
                    'name' => "Plot # {$number}",
                    // 'plot_number' => $number,
                    'width' => 38.0,
                    'length' => 80.0,
                    // 'marla' => marlaCal(38.0, 80.0)
                ]);
            }


            if( $number > 1  &&  $number <= 7 ){

                Plot::create([
                    'category_id' => 7,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 40.75,
                    'length' => 80.0,
                    // 'marla' => marlaCal(40.75, 80.0)
                ]);
            }



            if( $number > 8  &&  $number <= 11 ){

                Plot::create([
                    'category_id' => 7,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 40.74,
                    'length' => 80.0,
                    // 'marla' => marlaCal(40.75, 80.0)
                ]);


            }

            if( $number == 8 || $number == 13 ){

                Plot::create([
                    'category_id' => 9,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 69.75,
                    'length' => 74.0,
                    // 'marla' => marlaCal(69.75, 74.0)
                ]);


            }



            if($number == 12 || $number == 17 ){
                Plot::create([
                    'category_id' => 9,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 68.0,
                    'length' => 74.0,
                    // 'marla' => marlaCal(68.0, 74.0),
                    'is_corner' => true,
                    'is_forty_feet' => true
                ]);

            }

            if( $number > 13  && $number <= 16 ){
                Plot::create([
                    'category_id' => 6,
                    'name' => "Plot # {$number}",
                    // 'plot_number' => $number,
                    'width' => 40.75,
                    'length' => 68.0,
                    // 'marla' => marlaCal(40.75, 68.0),
                    'is_forty_feet' => true
                ]);
            }

            if( $number == 18 || $number == 19 || $number == 30 ){
                Plot::create([  
                    'category_id' => 6,
                    'name' => "Plot # {$number}",
                    // 'plot_number' => $number,
                    'width' => 41.0,
                    'length' => 67.0,
                    // 'marla' => marlaCal(41.0, 67.0),
                    'is_corner' => $number == 19 || 30 ? true : false ,
                    'is_forty_feet' => true
                ]);
            }


            if( $number > 19 && $number <= 23 ){
                Plot::create([
                    'category_id' => 6,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 40.0,
                    'length' => 68.0,
                    // 'marla' => marlaCal(40.0, 68.0),
                    'is_forty_feet' => true
                ]);
            }


            if( $number == 24 || $number == 25 ){
                Plot::create([
                    'category_id' => 8,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 61.5,
                    'length' => 67.0,
                    // 'marla' => marlaCal(61.5, 67.0),
                    'is_corner' => true,
                    'is_park_face' => true,
                    'is_forty_feet' => true
                ]);
            }


            if( $number > 25 && $number <= 29 ){
                Plot::create([
                    'category_id' => 5,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 40.0,
                    'length' => 55.0,
                    // 'marla' => marlaCal(40.0, 55.0),
                    'is_forty_feet' => false
                ]);
            }

            if( $number == 31 || $number == 43 ){
                Plot::create([
                    'category_id' => 7,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 52,
                    'length' => 67.0,
                    // 'marla' => marlaCal(52.0, 67.0),
                    'is_corner' => true,
                    'is_forty_feet' => true
                ]);
            }

            if( $number > 31 && $number <= 35 ){
                Plot::create([
                    'category_id' => 5,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 40.0,
                    'length' => 55.0,
                    // 'marla' => marlaCal(40.0, 55.0)
                ]);
            }

            if( $number == 36 || $number == 37 ){
                Plot::create([
                    'category_id' => 7,
                    'name' => "Plot # {$number}",
                    // 'plot_number' => $number,
                    'width' => 52.0,
                    'length' => 67.0,
                    // 'marla' => marlaCal(52.0, 67.0),
                    'is_corner' => true,
                    'is_park_face' => true,
                    'is_forty_feet' => true
                ]);
            }



            if( $number == 38 || $number == 42 ){
                Plot::create([
                    'category_id' => 3,
                    'name' => "Plot # {$number}",
                    // 'plot_number' => $number,
                    'width' => 37.0,
                    'length' => 49.0,
                    // 'marla' => marlaCal(37.0, 49.0)
                ]);
            }


            if( $number > 38 && $number <= 41 ){
                Plot::create([
                    'category_id' => 2,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 28.0,
                    'length' => 49.0,
                    // 'marla' => marlaCal(28.0, 49.0)
                ]);
            }

            if( $number == 44 ){

                Plot::create([
                    'category_id' => 2,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 32.0,
                    'length' => 49.5,
                    // 'marla' => marlaCal(32.0, 49.5)
                ]);

            }

            if( $number > 44  &&  $number <= 50 ){

                Plot::create([
                    'category_id' => 2,
                    'name' => "Plot # {$number}",
                    // 'plot_number' => $number,
                    'width' => 28.0,
                    'length' => 49.5,
                    // 'marla' => marlaCal(28.0, 49.5)
                ]);

            }

            if( $number == 51 || $number == 52 ){
                Plot::create([
                    'category_id' => 3,
                    'name' => "Plot # {$number}",
                    // 'plot_number' => $number,
                    'width' => 34.0,
                    'length' => 49.5,
                    // 'marla' => marlaCal(34.0, 49.5)
                ]);
            }

            if( $number == 53 || $number == 63 ){
                Plot::create([
                    'category_id' => 4,
                    'name' => "Plot #{$number}",
                    // 'plot_number' => $number,
                    'width' => 40.0,
                    'length' => 49.5,
                    // 'marla' => marlaCal(40.0, 49.5)
                ]);
            }

            if( $number == 54 || $number == 62 ){
                Plot::create([
                    'category_id' => 5,
                    'name' => "Plot {$number}",
                    // 'plot_number' => $number,
                    'width' => 48.0,
                    'length' => 49.5,
                    // 'marla' => marlaCal(48.0, 49.5)
                ]);
            }



            if( $number > 54 && $number <= 61 ){
                Plot::create([
                    'category_id' => 2,
                    'name' => "Plot {$number}",
                    // 'plot_number' => $number,
                    'width' => 28.0,
                    'length' => 49.5,
                    // 'marla' => marlaCal(28.0, 49.5)
                ]);
            }

            if( $number > 63 && $number <= 67 ){
                Plot::create([
                    'category_id' => 2,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 28.0,
                    'length' => 49.5,
                    // 'marla' => marlaCal(28.0, 49.5)
                ]);
            }

            if( $number == 68 ){
                Plot::create([
                    'category_id' => 3,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 35.0,
                    'length' => 48.5,
                    // 'marla' => marlaCal(35.0, 48.5)
                ]);
            }

            if( $number > 68  &&  $number <= 70 ){

                Plot::create([
                    'category_id' => 6,
                    'name' => "Plot #{$number}",
                     // 'plot_number' => $number,
                    'width' => 45.0,
                    'length' => 57.0,
                    // 'marla' => marlaCal(45.0, 57.0)
                ]);

            }

            if( $number == 71 ){
                Plot::create([
                    'category_id' => 8,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 0,
                    'length' => 0,
                    // 'marla' => marlaCal(0, 0)
                ]);
            }

            if( $number > 71 && $number <= 76 ){
                Plot::create([
                    'category_id' => 6,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 41.67,
                    'length' => 68.5,
                    // 'marla' => marlaCal(41.67, 68.5),
                    'is_corner' => $number == 76 ? true : false,
                    'is_forty_feet' => true
                ]);
            }


            if( $number == 77 ){
                Plot::create([
                    'category_id' => 3,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 0,
                    'length' => 0,
                    // 'marla' => marlaCal(0, 0)
                ]);
            }

            if( $number > 77  && $number <= 80 ){
                Plot::create([
                    'category_id' => 2,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 27.0,
                    'length' => 50.0,
                    // 'marla' => marlaCal(27.0, 50.0),
                    'is_corner' => $number == 80 ? true : false
                ]);
            }

            if( $number == 81 ){
                Plot::create([
                    'category_id' => 3,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 31.25,
                    'length' => 52.0,
                    // 'marla' => marlaCal(31.25, 52.0),
                    'is_corner' => true
                ]);
            }

            if( $number > 81  && $number <= 85 ){
                Plot::create([
                    'category_id' => 2,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 26.5,
                    'length' => 52.0,
                    // 'marla' => marlaCal(26.5, 52.0)
                ]);
            }


            if( $number > 85  && $number <= 88 ){
                Plot::create([
                    'category_id' => 6,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 40.0,
                    'length' => 68.0,
                    // 'marla' => marlaCal(40.0, 68.0),
                    'is_corner' => $number != 87 ? true : false,
                    'is_forty_feet' => true
                ]);
            }

            if( $number > 88  && $number <= 92 ){
                Plot::create([
                    'category_id' => 5,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 34.25,
                    'length' => 68.0,
                    // 'marla' => marlaCal(34.25, 68.0),
                    'is_corner' => $number == 92 ? true : false,
                    'is_forty_feet' => true
                ]);
            }

            if( $number > 92  && $number <= 101 ){
                Plot::create([
                    'category_id' => 2,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 27.0,
                    'length' => 50.0,
                    // 'marla' => marlaCal(27.0, 50.0)
                ]);
            }


            if( $number == 102 ){
                Plot::create([
                    'category_id' => 4,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 40.0,
                    'length' => 50.0,
                    // 'marla' => marlaCal(40.0, 50.0)
                ]);
            }

            if( $number > 102  && $number <= 120 ){
                Plot::create([
                    'category_id' => 2,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 27.0,
                    'length' => 50.0,
                    // 'marla' => marlaCal(27.0, 50.0),
                    'is_corner' => $number == 115 ? true : false
                ]);
            }

            if( $number == 121 || $number == 122 || $number == 129 || $number == 130 ){
                Plot::create([
                    'category_id' => 5,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 50.0,
                    'length' => 43.5,
                    // 'marla' => marlaCal(50.0, 43.5),
                    'is_corner' => true,
                    'is_park_face' => $number == 122 ? true: false
                ]);
            }

            if( $number > 122  && $number <= 128 ){
                Plot::create([
                    'category_id' => 2,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 27.0,
                    'length' => 50.0,
                    // 'marla' => marlaCal(27.0, 50.0),
                    'is_park_face' => $number < 126 ? true : false
                ]);
            }

            if( $number > 130  && $number <= 136 ){
                Plot::create([
                    'category_id' => 2,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 27.0,
                    'length' => 50.0,
                    // 'marla' => marlaCal(27.0, 50.0),
                    'is_corner' => $number == 136 ? true : false
                ]);
            }

            if( $number == 137  || $number == 143 ){
                Plot::create([
                    'category_id' => 3,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 42.0,
                    'length' => 39.0,
                    // 'marla' => marlaCal(42.0, 39.0),
                    'is_corner' =>  true
                ]);
            }

            if( $number ==  144 ){
                Plot::create([
                    'category_id' => 4,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 39.0,
                    'length' => 51.75,
                    // 'marla' => marlaCal(39.0, 51.75),
                    'is_corner' =>  true
                ]);
            }

            if( $number > 137  && $number <= 142 ){
                Plot::create([
                    'category_id' => 1,
                    'name' => "Plot # {$number}",
                     // 'plot_number' => $number,
                    'width' => 24.5,
                    'length' => 39.0,
                    // 'marla' => marlaCal(24.0, 39.0)
                ]);
            }

            if( $number > 144  && $number <= 147 ){
                Plot::create([
                    'category_id' => 1,
                    'name' => "Plot # {$number}",
                    // 'plot_number' => $number,
                    'width' => 24.5,
                    'length' => 39.0,
                    // 'marla' => marlaCal(24.0, 39.0)
                ]);
            }

        }

    }
}
