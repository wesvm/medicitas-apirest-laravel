<?php

namespace App\Http\Controllers\Patient;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\AppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function index()
    {
        $patient = auth()->user();
        $appointments = Appointment::where('patient_id', $patient->id)->get();
        return jsonResponse(data: [
            'appointments' => AppointmentResource::collection($appointments)
        ]);
    }

    public function store(AppointmentRequest $request)
    {
        $patient = auth()->user();
        return transactional(function () use ($request, $patient) {

            $existingAppointment = Appointment::where('doctor_id', $request->get('doctorId'))
                ->where('appointment_date', $request->get('appointmentDate'))
                ->exists();

            if ($existingAppointment) {
                return jsonResponse(
                    status: 409,
                    message: 'The selected time is not available.'
                );
            }

            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $request->get('doctorId'),
                'appointment_date' => $request->get('appointmentDate'),
                'reason' => $request->get('reason'),
                'status' => AppointmentStatus::SCHEDULED->value
            ]);

            return jsonResponse(
                status: 201,
                message: 'Appointment created successfully',
                data: ['appointment' => new AppointmentResource($appointment)]
            );
        });
    }

    public function show(string $id)
    {
        $patient = auth()->user();
        $appointment = Appointment::where('patient_id', $patient->id)
            ->findOrFail($id);
        return jsonResponse(data: [
            'appointment' => new AppointmentResource($appointment)
        ]);
    }

    public function update(AppointmentRequest $request, string $id)
    {
        $patient = auth()->user();
        $appointment = Appointment::where('patient_id', $patient->id)
            ->findOrFail($id);

        return transactional(function () use ($request, $appointment) {
            $appointment->doctor_id = $request->get('doctorId');
            $appointment->appointment_date = $request->get('appointmentDate');
            $appointment->reason = $request->get('reason');

            if ($appointment->isDirty()) {
                $appointment->save();

                return jsonResponse(
                    message: 'Appointment updated successfully',
                    data: ['appointment' => new AppointmentResource($appointment)]
                );
            }

            return jsonResponse(
                message: 'No changes detected',
                data: ['appointment' => new AppointmentResource($appointment)]
            );
        });
    }

    public function destroy(string $id)
    {
        $patient = auth()->user();
        $appointment = Appointment::where('patient_id', $patient->id)
            ->findOrFail($id);

        $appointment->delete();
        return jsonResponse(message: 'Appointment deleted successfully.');
    }
}
