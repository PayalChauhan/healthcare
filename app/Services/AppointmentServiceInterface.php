<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentServiceInterface
{
    public function book(int $userId, array $data): Appointment;
    public function list(int $userId): Collection;
    public function cancel(int $userId, int $appointmentId): void;
    public function complete(int $userId, int $appointmentId): void;
}