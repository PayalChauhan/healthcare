<?php

namespace App\Constants;

class AppointmentConstants
{
    // Messages
    public const TIME_SLOT_NA = 'Time slot not available';
    public const MSG_CANCELLED_SUCCESS = 'Cancelled successfully';
    public const MSG_COMPLETED_SUCCESS = 'Marked as completed';
    public const MSG_MAX_DURATION_EXCEEDED = 'Appointment duration may not exceed 1 hour.';
    public const MSG_VALIDATION_FAILED = 'Validation failed.';
    public const MSG_START_TIME_IN_PAST = 'Appointment start time must be in the future.';
    public const MSG_END_TIME_BEFORE_START = 'Appointment end time must be after the start time.';
    public const CANCELLATION_WINDOW_HOURS   = 24;
    public const MSG_CANNOT_CANCEL_PAST            = 'Cannot cancel an appointment that has already started.';
    public const MSG_CANNOT_CANCEL_WITHIN_24_HOURS = 'Cannot cancel within 24 hours of the appointment.';

    // Status values
    public const STATUS_BOOKED = 'booked';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    //HTTP status
    public const HTTP_CONFLICT = 409;
    public const HTTP_CREATED = 201;
    public const HTTP_OK = 200;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_UNPROCESSABLE = 422;
}
