<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookAppointmentRequest;
use App\Services\AppointmentServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Constants\AppointmentConstants;
use App\Exceptions\TimeSlotUnavailableException;
use App\Exceptions\CancellationWindowExpiredException;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    private AppointmentServiceInterface $service;

    public function __construct(AppointmentServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Handles the process of booking an appointment for a user
     *
     * @author Payal
     * @param BookAppointmentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function book(BookAppointmentRequest $request): JsonResponse
    {
        $data   = $request->validated();
        $userId = auth()->id();

        try {
            $appointment = $this->service->book($userId, $data);
            return response()->json(
                $appointment,
                Response::HTTP_CREATED
            );
        } catch (TimeSlotUnavailableException $e) {
            return response()->json(
                [
                    'message' => $e->getMessage()
                ],
                $e->getCode()
            );
        }
    }

    /**
     * Retrieves the list of appointments for the user
     *
     * @author Payal
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(): JsonResponse
    {
        $list = $this->service->list(auth()->id());
        return response()->json(
            $list,
            Response::HTTP_OK
        );
    }

    /**
     * Cancel the specified appointment for the user.
     *
     * @author Payal
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(int $id): JsonResponse
    {
        try {
            $this->service->cancel(auth()->id(), $id);
            return response()->json(
                [
                    'message' => AppointmentConstants::MSG_CANCELLED_SUCCESS
                ],
                Response::HTTP_OK
            );
        } catch (CancellationWindowExpiredException $e) {
            return response()->json(
                [
                    'message' => $e->getMessage()
                ],
                $e->getCode()
            );
        }
    }

    /**
     * Function to mark an appointment as completed
     *
     * @author Payal
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(int $id): JsonResponse
    {
        $this->service->complete(auth()->id(), $id);
        return response()->json(
            [
                'message' => AppointmentConstants::MSG_COMPLETED_SUCCESS
            ],
            Response::HTTP_OK
        );
    }
}
