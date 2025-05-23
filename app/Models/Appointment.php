<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id', 'healthcare_professional_id',
        'appointment_start_time', 'appointment_end_time', 'status'
    ];

    /**
     * Defines a relationship where an appointment belongs to a user
     *
     * @author Payal
     * @return object
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Defines a relationship where an appointment belongs to a healthcare professional
     *
     * @author Payal
     * @return object
     */
    public function healthcareProfessional() {
        return $this->belongsTo(HealthcareProfessional::class);
    }
}