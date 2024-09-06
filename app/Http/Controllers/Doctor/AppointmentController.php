<?php

namespace App\Http\Controllers\Doctor;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Http\Resources\DoctorAppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $doctor = auth()->user();
        $page = $request->query('page', 1);
        $perPage = $request->query('perPage', 10);
        $search = $request->query('search', '');
        $status = $request->query('status');
        $date = $request->query('date');

        $query = Appointment::where('doctor_id', $doctor->id)
            ->with('patient.user')
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($date, function ($query, $appointmentDate) {
                $query->whereDate('appointment_date', $appointmentDate);
            })
            ->whereHas('patient.user', function ($query) use ($search) {
                $query->search($search);
            });

        $appointments = $query->sort()->paginate($perPage, ['*'], 'page', $page);

        return jsonResponse(data: [
            'appointments' => DoctorAppointmentResource::collection($appointments),
            'pagination' => [
                'total' => $appointments->total(),
                'current_page' => $appointments->currentPage(),
                'per_page' => $appointments->perPage(),
                'last_page' => $appointments->lastPage(),
                'next_page_url' => $appointments->nextPageUrl(),
                'prev_page_url' => $appointments->previousPageUrl(),
            ]
        ]);
    }

    public function store(AppointmentRequest $request)
    {
        $doctor = auth()->user();
        $appointmentDate = $request->get('appointmentDate');
        $patientId = $request->get('patientId');

        $doctorHasAppointment = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', $appointmentDate)
            ->exists();

        $patientHasAppointment = Appointment::where('patient_id', $patientId)
            ->where('appointment_date', $appointmentDate)
            ->exists();

        if ($doctorHasAppointment || $patientHasAppointment) {
            return jsonResponse(
                status: 409,
                message: 'The selected time is not available.'
            );
        }

        return transactional(function () use ($request, $doctor, $patientId, $appointmentDate) {
            $appointment = Appointment::create([
                'patient_id' => $patientId,
                'doctor_id' => $doctor->id,
                'appointment_date' => $appointmentDate,
                'reason' => $request->get('reason'),
                'status' => AppointmentStatus::SCHEDULED->value
            ]);

            return jsonResponse(
                status: 201,
                message: 'Appointment created successfully',
                data: ['appointment' => new DoctorAppointmentResource($appointment)]
            );
        });
    }

    public function show(string $id)
    {
        $doctor = auth()->user();
        $appointment = Appointment::where('doctor_id', $doctor->id)
            ->with('patient.user')
            ->findOrFail($id);
        return jsonResponse(data: [
            'appointment' => new DoctorAppointmentResource($appointment)
        ]);
    }

    public function update(AppointmentRequest $request, string $id)
    {
        $doctor = auth()->user();
        $appointmentDate = $request->get('appointmentDate');

        $appointment = Appointment::where('doctor_id', $doctor->id)
            ->findOrFail($id);

        $patientId = $request->get('patientId') ?? $appointment->patient_id;

        $doctorHasAppointment = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', $appointmentDate)
            ->where('id', '!=', $id)
            ->exists();

        $patientHasAppointment = Appointment::where('patient_id', $patientId)
            ->where('appointment_date', $appointmentDate)
            ->where('id', '!=', $id)
            ->exists();

        if ($doctorHasAppointment || $patientHasAppointment) {
            return jsonResponse(
                status: 409,
                message: 'The selected time is not available.'
            );
        }

        return transactional(function () use ($request, $appointment, $patientId, $appointmentDate) {
            $appointment->patient_id = $patientId;
            $appointment->appointment_date = $appointmentDate;
            $appointment->reason = $request->get('reason');
            $appointment->status = $request->get('status') ?? AppointmentStatus::SCHEDULED->value;

            if ($appointment->isDirty()) {
                $appointment->save();

                return jsonResponse(
                    message: 'Appointment status updated successfully',
                    data: ['appointment' => new DoctorAppointmentResource($appointment)]
                );
            }

            return jsonResponse(
                message: 'No changes detected',
                data: ['appointment' => new DoctorAppointmentResource($appointment)]
            );
        });
    }

    public function destroy(string $id)
    {
        $doctorId = auth()->user()->id;
        $appointment = Appointment::where('doctor_id', $doctorId)
            ->findOrFail($id);

        $appointment->delete();
        return jsonResponse(message: 'Appointment deleted successfully.');
    }
}
