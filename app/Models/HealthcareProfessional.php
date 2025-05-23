<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthcareProfessional extends Model
{
    protected $fillable = ['name', 'specialty'];

    /**
     * Defines a relationship where a healthcare professional has many appointments
     *
     * @author Payal
     * @return object
     */
    public function appointments() {
        return $this->hasMany(Appointment::class);
    }
}