<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\UpdatePatientProfileRequest;
use App\Http\Resources\PatientResource;

class PatientProfileController extends Controller
{
    /**
     * Display the authenticated patient's profile.
     */
    public function show()
    {
        $patient = auth()->user();
        return jsonResponse(data: ['user' => new PatientResource($patient)]);
    }

    /**
     * Update the authenticated patient's profile.
     */
    public function update(UpdatePatientProfileRequest $request)
    {
        $patient = auth()->user();

        return transactional(function () use ($request, $patient) {

            $patient->email = $request->get('email');
            $patient->phone = $request->get('phone');

            $patient->patient->address = $request->get('address');
            $patient->patient->occupation = $request->get('occupation');
            $patient->patient->insurance_type = $request->get('insuranceType');
            $patient->patient->emergency_contact_phone = $request->get('emergencyContactPhone');

            if ($patient->isDirty() || $patient->patient->isDirty()) {
                $patient->save();
                $patient->patient->save();

                return jsonResponse(
                    message: 'Profile updated successfully',
                    data: ['user' => new PatientResource($patient)]
                );
            }

            return jsonResponse(
                message: 'No changes detected',
                data: ['user'=> new PatientResource($patient)]
            );
        });
    }
}
