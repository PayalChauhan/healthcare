<?php

namespace Database\Factories;

use App\Models\HealthcareProfessional;
use Illuminate\Database\Eloquent\Factories\Factory;

class HealthcareProfessionalFactory extends Factory
{
    protected $model = HealthcareProfessional::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'      => $this->faker->name,
            'specialty' => $this->faker->randomElement([
                'Cardiology',
                'Dermatology',
                'Orthopedics',
                'Pediatrics'
            ]),
        ];
    }
}
