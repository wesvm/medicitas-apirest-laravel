<?php

namespace App\Http\Controllers\Doctor;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\ConsultationRequest;
use App\Http\Resources\ConsultationResource;
use App\Models\Appointment;
use App\Models\Consultation;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $doctor = auth()->user();
        $page = $request->query('page', 1);
        $perPage = $request->query('perPage', 10);

        $consultations = Consultation::where('doctor_id', $doctor->id)
            ->sort()
            ->paginate($perPage, ['*'], 'page', $page);

        return jsonResponse(data: [
            'consultations' => ConsultationResource::collection($consultations),
            'pagination' => [
                'total' => $consultations->total(),
                'current_page' => $consultations->currentPage(),
                'per_page' => $consultations->perPage(),
                'last_page' => $consultations->lastPage(),
                'next_page_url' => $consultations->nextPageUrl(),
                'prev_page_url' => $consultations->previousPageUrl(),
            ]
        ]);
    }

    public function store(ConsultationRequest $request)
    {
        $doctorId = auth()->user()->id;
        $appointmentId = $request->input('appointmentId');
        $appointment = null;

        if ($appointmentId) {
            $appointment = Appointment::findOrFail($appointmentId);

            if ($appointment->doctor_id != $doctorId) {
                return jsonResponse(status: 403, message: 'This doctor does not belong to this appointment');
            }
        }

        return transactional(function () use ($request, $doctorId, $appointment) {
            $consultation = Consultation::create([
                'appointment_id' => $appointment->id ?? null,
                'consultation_date' => $request->get('consultationDate'),
                'consultation_reason' => $request->get('consultationReason'),
                'diagnosis' => $request->get('diagnosis'),
                'treatment' => $request->get('treatment'),
                'observations' => $request->get('observations') ?? null,
                'patient_id' => $appointment ? $appointment->patient_id : $request->get('patientId'),
                'doctor_id' => $doctorId,
                'next_appointment_id' => $request->get('nextAppointment') ?? null,
            ]);

            if ($appointment) {
                $appointment->status = AppointmentStatus::COMPLETED->value;
                $appointment->save();
            }


            return jsonResponse(
                status: 201,
                message: 'Consultation created successfully',
                data: ['consultation' => new ConsultationResource($consultation)]
            );
        });
    }

    public function show(string $id)
    {
        $doctor = auth()->user();
        $consultation = Consultation::where('doctor_id', $doctor->id)
            ->with('patient')
            ->findOrFail($id);

        return jsonResponse(data: [
            'consultation' => new ConsultationResource($consultation)
        ]);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
