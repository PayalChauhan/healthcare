<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\AppointmentService;
use App\Models\User;
use App\Models\HealthcareProfessional;
use App\Models\Appointment;
use Carbon\Carbon;
use App\Exceptions\InvalidAppointmentTimeException;
use App\Exceptions\TimeSlotUnavailableException;
use App\Exceptions\CancellationWindowExpiredException;
use App\Constants\AppointmentConstants;

class AppointmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private AppointmentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AppointmentService::class);
    }

    public function test_can_book_appointment_successfully()
    {
        Carbon::setTestNow('2025-06-01 09:00:00');
        $user = User::factory()->create();
        $hp   = HealthcareProfessional::factory()->create();
        $data = [
            'healthcare_professional_id' => $hp->id,
            'appointment_start_time'     => '2025-06-02 10:00:00',
            'appointment_end_time'       => '2025-06-02 11:00:00',
        ];
        $appt = $this->service->book($user->id, $data);
        $this->assertDatabaseHas('appointments', [
            'id'                          => $appt->id,
            'user_id'                     => $user->id,
            'healthcare_professional_id' => $hp->id,
            'status'                     => AppointmentConstants::STATUS_BOOKED,
        ]);
    }

    public function test_start_time_in_past_throws_exception()
    {
        Carbon::setTestNow('2025-06-05 12:00:00');
        $user = User::factory()->create();
        $hp   = HealthcareProfessional::factory()->create();
        $this->expectException(InvalidAppointmentTimeException::class);
        $this->service->book($user->id, [
            'healthcare_professional_id' => $hp->id,
            'appointment_start_time'     => '2025-06-01 10:00:00', // in the past
            'appointment_end_time'       => '2025-06-01 11:00:00',
        ]);
    }

    public function test_end_time_before_start_throws_exception()
    {
        Carbon::setTestNow('2025-06-01 09:00:00');
        $user = User::factory()->create();
        $hp   = HealthcareProfessional::factory()->create();
        $this->expectException(InvalidAppointmentTimeException::class);
        $this->service->book($user->id, [
            'healthcare_professional_id' => $hp->id,
            'appointment_start_time'     => '2025-06-02 11:00:00',
            'appointment_end_time'       => '2025-06-02 10:00:00', // before start
        ]);
    }

    public function test_max_duration_exceeded_throws_exception()
    {
        Carbon::setTestNow('2025-06-01 09:00:00');
        $user = User::factory()->create();
        $hp   = HealthcareProfessional::factory()->create();
        $this->expectException(InvalidAppointmentTimeException::class);
        $this->service->book($user->id, [
            'healthcare_professional_id' => $hp->id,
            'appointment_start_time'     => '2025-06-02 10:00:00',
            'appointment_end_time'       => '2025-06-03 11:00:00', // >1h span
        ]);
    }

    public function test_conflicting_slot_throws_exception()
    {
        Carbon::setTestNow('2025-06-01 09:00:00');
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $hp    = HealthcareProfessional::factory()->create();
        Appointment::factory()->create([
            'user_id'                     => $user1->id,
            'healthcare_professional_id' => $hp->id,
            'appointment_start_time'     => '2025-06-02 10:00:00',
            'appointment_end_time'       => '2025-06-02 11:00:00',
        ]);
        $this->expectException(TimeSlotUnavailableException::class);
        $this->service->book($user2->id, [
            'healthcare_professional_id' => $hp->id,
            'appointment_start_time'     => '2025-06-02 10:30:00',
            'appointment_end_time'       => '2025-06-02 11:30:00',
        ]);
    }

    public function test_cancel_before_window_expires_throws_exception()
    {
        Carbon::setTestNow('2025-06-02 09:00:00');
        $user = User::factory()->create();
        $hp   = HealthcareProfessional::factory()->create();
        $appt = Appointment::factory()->create([
            'user_id'                     => $user->id,
            'healthcare_professional_id' => $hp->id,
            'appointment_start_time'     => Carbon::now()->addHours(10),
            'appointment_end_time'       => Carbon::now()->addHours(11),
        ]);
        $this->expectException(CancellationWindowExpiredException::class);
        $this->service->cancel($user->id, $appt->id);
    }

    public function test_cancel_after_window_succeeds()
    {
        Carbon::setTestNow('2025-06-01 00:00:00');
        $user = User::factory()->create();
        $hp   = HealthcareProfessional::factory()->create();
        $appt = Appointment::factory()->create([
            'user_id'                     => $user->id,
            'healthcare_professional_id' => $hp->id,
            'appointment_start_time'     => Carbon::now()->addDays(2),
            'appointment_end_time'       => Carbon::now()->addDays(2)->addHour(),
        ]);
        $this->service->cancel($user->id, $appt->id);
        $this->assertDatabaseHas('appointments', [
            'id'     => $appt->id,
            'status' => AppointmentConstants::STATUS_CANCELLED,
        ]);
    }

    public function test_complete_marks_status_completed()
    {
        $user = User::factory()->create();
        $hp   = HealthcareProfessional::factory()->create();
        $appt = Appointment::factory()->create([
            'user_id'                     => $user->id,
            'healthcare_professional_id' => $hp->id,
            'appointment_start_time'     => Carbon::now()->addDay(),
            'appointment_end_time'       => Carbon::now()->addDay()->addHour(),
        ]);
        $this->service->complete($user->id, $appt->id);
        $this->assertDatabaseHas('appointments', [
            'id'     => $appt->id,
            'status' => AppointmentConstants::STATUS_COMPLETED,
        ]);
    }
}
