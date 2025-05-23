<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Constants\AppointmentConstants;
use Carbon\Carbon;

class BookAppointmentRequest extends FormRequest
{
    /**
     * Validation rules for booking an appointment.
     *
     * @author Payal
     * @return array
     */
    public function rules()
    {
        return [
            'healthcare_professional_id' => 'required|integer|exists:healthcare_professionals,id',
            'appointment_start_time'     => 'required|date|after:now',
            'appointment_end_time' => [
                'required',
                'date',
                'after:appointment_start_time',
                function ($attribute, $value, $fail) {
                    $start = Carbon::parse($this->input('appointment_start_time'));
                    $end   = Carbon::parse($value);
                    if ($end->diffInHours($start) > 1) {
                        $fail(AppointmentConstants::MSG_MAX_DURATION_EXCEEDED);
                    }
                },
            ],
        ];
    }

    /**
     * Handle a failed validation attempt by throwing a JSON error response.
     *
     * @author Payal
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(
                [
                    'message' => AppointmentConstants::MSG_VALIDATION_FAILED,
                    'errors'  => $validator->errors(),
                ],
                AppointmentConstants::HTTP_UNPROCESSABLE
            )
        );
    }
}
