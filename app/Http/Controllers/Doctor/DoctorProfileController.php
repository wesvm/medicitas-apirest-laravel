<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\UpdateDoctorProfileRequest;
use App\Http\Resources\DoctorResource;

class DoctorProfileController extends Controller
{
    /**
     * Display the authenticated patient's profile.
     */
    public function show()
    {
        $doctor = auth()->user();
        return jsonResponse(data: ['user' => new DoctorResource($doctor)]);
    }

    /**
     * Update the authenticated patient's profile.
     */
    public function update(UpdateDoctorProfileRequest $request)
    {
        $doctor = auth()->user();

        return transactional(function () use ($request, $doctor) {

            $doctor->email = $request->get('email');
            $doctor->phone = $request->get('phone');

            $doctor->doctor->schedule_id = $request->get('scheduleId');

            if ($doctor->isDirty() || $doctor->doctor->isDirty()) {
                $doctor->save();
                $doctor->doctor->save();

                return jsonResponse(
                    message: 'Profile updated successfully',
                    data: ['user' => new DoctorResource($doctor)]
                );
            }

            return jsonResponse(
                message: 'No changes detected',
                data: ['user'=> new DoctorResource($doctor)]
            );
        });
    }
}
