<?php

namespace App\Services;

use App\Models\Appointment;
use App\Constants\AppointmentConstants;
use App\Exceptions\TimeSlotUnavailableException;
use App\Exceptions\CancellationWindowExpiredException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\InvalidAppointmentTimeException;


class AppointmentService implements AppointmentServiceInterface
{

    /**
     * Service method to book an appointment for a specific user.
     *
     * @author Payal
     * @param int $userId
     * @param array $data
     * @return \App\Models\Appointment
     *
     * @throws \App\Exceptions\InvalidAppointmentTimeException
     * @throws \App\Exceptions\TimeSlotUnavailableException
     */
    public function book(int $userId, array $data): Appointment
    {
        $start = Carbon::parse($data['appointment_start_time']);
        $end   = Carbon::parse($data['appointment_end_time']);

        if ($start->isPast()) {
            throw new InvalidAppointmentTimeException(
                AppointmentConstants::MSG_START_TIME_IN_PAST,
                AppointmentConstants::HTTP_UNPROCESSABLE
            );
        }
        if (! $end->greaterThan($start)) {
            throw new InvalidAppointmentTimeException(
                AppointmentConstants::MSG_END_TIME_BEFORE_START,
                AppointmentConstants::HTTP_UNPROCESSABLE
            );
        }
        if ($end->diffInHours($start) > 1) {
            throw new InvalidAppointmentTimeException(
                AppointmentConstants::MSG_MAX_DURATION_EXCEEDED,
                AppointmentConstants::HTTP_UNPROCESSABLE
            );
        }
        $conflict = Appointment::where('healthcare_professional_id', $data['healthcare_professional_id'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween(
                    'appointment_start_time',
                    [
                        $start->toDateTimeString(),
                        $end->toDateTimeString()
                    ]
                )
                    ->orWhereBetween(
                        'appointment_end_time',
                        [
                            $start->toDateTimeString(),
                            $end->toDateTimeString()
                        ]
                    );
            })
            ->exists();
        if ($conflict) {
            throw new TimeSlotUnavailableException(
                AppointmentConstants::TIME_SLOT_NA,
                AppointmentConstants::HTTP_CONFLICT
            );
        }
        return Appointment::create([
            'user_id'                     => $userId,
            'healthcare_professional_id' => $data['healthcare_professional_id'],
            'appointment_start_time'     => $start->toDateTimeString(),
            'appointment_end_time'       => $end->toDateTimeString(),
            'status'                     => AppointmentConstants::STATUS_BOOKED,
        ]);
    }

    /**
     * Retrieve all appointments for a given user.
     *
     * @author Payal
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function list(int $userId): Collection
    {
        return Appointment::where('user_id', $userId)->get();
    }

    /**
     * Cancel a specific appointment for a user if within the allowed window.
     *
     * @author Payal
     * @param int $userId
     * @param int $appointmentId
     * @throws \App\Exceptions\CancellationWindowExpiredException
     * @return void
     */
    public function cancel(int $userId, int $appointmentId): void
    {
        $appointment = Appointment::where('id', $appointmentId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $start = Carbon::parse($appointment->appointment_start_time);
        $now   = Carbon::now();
        if ($now->greaterThanOrEqualTo($start)) {
            throw new CancellationWindowExpiredException(
                AppointmentConstants::MSG_CANNOT_CANCEL_PAST,
                AppointmentConstants::HTTP_FORBIDDEN
            );
        }
        if ($now->diffInHours($start) < AppointmentConstants::CANCELLATION_WINDOW_HOURS) {
            throw new CancellationWindowExpiredException(
                AppointmentConstants::MSG_CANNOT_CANCEL_WITHIN_24_HOURS,
                AppointmentConstants::HTTP_FORBIDDEN
            );
        }
        $appointment->update([
            'status' => AppointmentConstants::STATUS_CANCELLED,
        ]);
    }

    /**
     * Mark a specific appointment as completed for a user.
     *
     * @author Payal
     * @param int $userId
     * @param int $appointmentId
     * @return void
     */
    public function complete(int $userId, int $appointmentId): void
    {
        $appt = Appointment::where('id', $appointmentId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $appt->update(['status' => AppointmentConstants::STATUS_COMPLETED]);
    }
}
