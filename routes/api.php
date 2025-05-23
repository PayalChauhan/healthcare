<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthcareProfessionalController;
use App\Http\Controllers\AppointmentController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('professionals', [HealthcareProfessionalController::class, 'index']);
    Route::post('appointments', [AppointmentController::class, 'book']);
    Route::get('appointments', [AppointmentController::class, 'list']);
    Route::delete('appointments/{id}', [AppointmentController::class, 'cancel']);
    Route::patch('appointments/{id}/complete', [AppointmentController::class, 'complete']);
});
