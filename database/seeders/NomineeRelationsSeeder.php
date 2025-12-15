<?php

namespace Database\Seeders;

use App\Models\NomineeRelation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NomineeRelationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $relations = [
            'Father','Mother','Brother','Sister', 
            'Wife', 'Husband','Son','Daughter',
            'Grand Father', 'Grand-mother',
            'Other',
        ];

        foreach ($relations as $relation) {
            NomineeRelation::firstOrCreate(['name' => $relation]);
        }
    }
}
