<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\Patient\PatientProfileController;
use Illuminate\Support\Facades\Route;

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
    'middleware' => ['auth:api', 'role:admin'],
], function () {
    Route::apiResource('admins', AdminController::class);
    Route::apiResource('patients', PatientController::class);
});

Route::group([
    'middleware' => ['auth:api', 'role:patient'],
    'prefix' => 'patient'
], function () {
    Route::get('profile', [PatientProfileController::class, 'show']);
    Route::put('profile', [PatientProfileController::class, 'update']);
});
