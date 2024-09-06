<?php

namespace App\Http\Controllers\Patient;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Http\Resources\PatientAppointmentResource;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function index()
    {
        $patient = auth()->user();
        $appointments = Appointment::where('patient_id', $patient->id)->get();
        return jsonResponse(data: [
            'appointments' => PatientAppointmentResource::collection($appointments)
        ]);
    }

    public function store(AppointmentRequest $request)
    {
        $patient = auth()->user();
        $doctorId = $request->input('doctorId');
        $appointmentDate = $request->input('appointmentDate');

        $doctorHasAppointment = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $appointmentDate)
            ->exists();

        $patientHasAppointment = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', $appointmentDate)
            ->exists();

        if ($doctorHasAppointment || $patientHasAppointment) {
            return jsonResponse(
                status: 409,
                message: 'The selected time is not available.'
            );
        }

        return transactional(function () use ($request, $patient) {
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
                data: ['appointment' => new PatientAppointmentResource($appointment)]
            );
        });
    }

    public function show(string $id)
    {
        $patient = auth()->user();
        $appointment = Appointment::where('patient_id', $patient->id)
            ->findOrFail($id);
        return jsonResponse(data: [
            'appointment' => new PatientAppointmentResource($appointment)
        ]);
    }

    public function update(AppointmentRequest $request, string $id)
    {
        $patient = auth()->user();
        $doctorId = $request->input('doctorId');
        $appointmentDate = $request->input('appointmentDate');

        $appointment = Appointment::where('patient_id', $patient->id)
            ->findOrFail($id);

        $doctorHasAppointment = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $appointmentDate)
            ->where('id', '!=', $id)
            ->exists();

        $patientHasAppointment = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', $appointmentDate)
            ->where('id', '!=', $id)
            ->exists();

        if ($doctorHasAppointment || $patientHasAppointment) {
            return jsonResponse(
                status: 409,
                message: 'The selected time is not available.'
            );
        }

        return transactional(function () use ($request, $appointment) {
            $appointment->doctor_id = $request->get('doctorId');
            $appointment->appointment_date = $request->get('appointmentDate');
            $appointment->reason = $request->get('reason');

            if ($appointment->isDirty()) {
                $appointment->save();

                return jsonResponse(
                    message: 'Appointment updated successfully',
                    data: ['appointment' => new PatientAppointmentResource($appointment)]
                );
            }

            return jsonResponse(
                message: 'No changes detected',
                data: ['appointment' => new PatientAppointmentResource($appointment)]
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
