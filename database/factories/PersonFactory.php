<?php

namespace Database\Factories;

use App\Models\Occupation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{

    protected $model = \App\Models\Person::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name'             => $this->faker->firstName,
            'last_name'              => $this->faker->lastName,
            'father_or_husband_name' => $this->faker->name,
            'gender'                 => $this->faker->randomElement(['male', 'female']),
            // Ensure occupation exists
            'occupation_id' => Occupation::inRandomOrder()->value('id') 
                ?? Occupation::factory(),

            'mobile'                 => $this->faker->numerify('03##-#######'),
            'phone'                  => $this->faker->numerify('042#######'),
            'whatsapp'               => $this->faker->numerify('03##-#######'),
            'email'                  => $this->faker->safeEmail,
            'cnic'                   => $this->faker->numerify('#####-#######-#'),
            'dob'                    => $this->faker->date(),
            'postal_address'         => $this->faker->address,
            'residential_address'    => $this->faker->address,
            'person_img'             => null,
        ];
    }
}
