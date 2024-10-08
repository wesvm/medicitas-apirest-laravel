<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\DoctorScheduleController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;

use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\DoctorProfileController;
use App\Http\Controllers\Doctor\ConsultationController;

use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\PatientProfileController;


Route::prefix('auth')->group(function () {
    Route::post('login', AuthController::class.'@login');
    Route::post('logout', AuthController::class.'@logout');
    Route::post('refresh', AuthController::class.'@refresh');
    Route::post('me', AuthController::class.'@me');
});

Route::prefix('password')->group(function () {
    Route::post('/forgot', [PasswordController::class, 'forgot']);
    Route::put('/reset', [PasswordController::class, 'reset']);
    Route::put('/update', [PasswordController::class, 'update']);
});

Route::group([
    'middleware' => 'auth:api',
], function () {
    Route::apiResource('admins', AdminController::class);
    Route::apiResource('patients', PatientController::class);
    Route::apiResource('doctors', DoctorController::class);
    Route::apiResource('users', UserController::class)->except(['store', 'destroy']);

    Route::get('schedules', [DoctorScheduleController::class, 'index']);
});

Route::group([
    'middleware' => ['auth:api', 'role:admin'],
    'prefix' => 'admin'
], function () {
    Route::get('reports', [ReportController::class, 'index']);
});

Route::group([
    'middleware' => ['auth:api', 'role:doctor'],
    'prefix' => 'doctor'
], function () {
    Route::get('profile', [DoctorProfileController::class, 'show']);
    Route::put('profile', [DoctorProfileController::class, 'update']);

    Route::apiResource('appointments', DoctorAppointmentController::class);
    Route::apiResource('consultations', ConsultationController::class);
});

Route::group([
    'middleware' => ['auth:api', 'role:patient'],
    'prefix' => 'patient'
], function () {
    Route::get('profile', [PatientProfileController::class, 'show']);
    Route::put('profile', [PatientProfileController::class, 'update']);

    Route::apiResource('appointments', PatientAppointmentController::class);
});
