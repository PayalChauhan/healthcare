<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HealthcareProfessional;

class HealthcareProfessionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = ['Cardiology','Dermatology','Neurology','Pediatrics'];
        foreach ($specialties as $spec) {
            HealthcareProfessional::create([
                'name' => "Dr. $spec",
                'specialty' => $spec
            ]);
        }
    }
}
