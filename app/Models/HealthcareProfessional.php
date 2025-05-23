<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HealthcareProfessional extends Model
{
    use HasFactory;
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