<?php

namespace Database\Seeders;

use App\Models\Occupation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OccupationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $occupations = [
            'Businessman',
            'Teacher',
            'Engineer',
            'Doctor',
            'Lawyer',
            'Student',
            'Freelancer',
            'Farmer',
            'Other',
        ];

        foreach ($occupations as $occupation) {
            Occupation::firstOrCreate(['name' => $occupation]);
        }
    }
}
