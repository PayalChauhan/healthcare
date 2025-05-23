<?php

namespace App\Http\Controllers;

use App\Models\HealthcareProfessional;

class HealthcareProfessionalController extends Controller
{
    /**
     * Function get list of all healthcare professionals
     *
     * @author Payal
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(HealthcareProfessional::all());
    }
}
