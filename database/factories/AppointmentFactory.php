<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\User;
use App\Models\HealthcareProfessional;
use App\Constants\AppointmentConstants;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $start = Carbon::now()
            ->addDays(rand(1, 7))
            ->setHour(rand(8, 16))
            ->setMinute(0)
            ->setSecond(0);

        $end = (clone $start)->addHour();
        return [
            'user_id'                     => User::factory(),
            'healthcare_professional_id' => HealthcareProfessional::factory(),
            'appointment_start_time'     => $start->toDateTimeString(),
            'appointment_end_time'       => $end->toDateTimeString(),
            'status'                     => AppointmentConstants::STATUS_BOOKED,
        ];
    }
}
