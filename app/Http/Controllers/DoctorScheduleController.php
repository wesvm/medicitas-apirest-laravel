<?php

namespace App\Http\Controllers;

use App\Http\Resources\DoctorScheduleResource;
use App\Models\DoctorSchedule;

class DoctorScheduleController extends Controller
{
    public function index()
    {
        return jsonResponse(
            data: [
                'schedules' => DoctorScheduleResource::collection(DoctorSchedule::all())
            ]
        );
    }
}
