<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportRequest;
use App\Enums\Specialty;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;

class ReportController extends Controller
{
    public function index(ReportRequest $request)
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $school = $request->get('school');

        $appsQuery = $this->appointmentsQuery($startDate, $endDate);
        $daysQuery = $this->daysQuery($startDate, $endDate);

        if ($school !== 'All') {
            if ($school === 'Other') {
                $appsQuery->whereNull('patients.professional_school');
                $daysQuery->whereNull('patients.professional_school');
            } else {
                $appsQuery->where('patients.professional_school', $school);
                $daysQuery->where('patients.professional_school', $school);
            }
        }

        $resultsApps = $appsQuery->get()->keyBy('specialty');
        $resultsDays = $daysQuery->get()->groupBy('day');

        $totalAppointments = $resultsApps->sum('total_appointments');
        $totalStudents = $resultsApps->sum('total_students');
        $totalNoStudents = $resultsApps->sum('total_no_students');

        $specialties = array_map(function ($specialty) use ($resultsApps) {
            return [
                'name' => $specialty,
                'total' => $resultsApps->has($specialty) ? $resultsApps->get($specialty)->total_appointments : 0,
            ];
        }, Specialty::values());

        $days = $resultsDays->map(function ($dayAppointments, $day) {
            $specialties = array_map(function ($specialty) use ($dayAppointments) {
                $appointment = $dayAppointments->firstWhere('specialty', $specialty);
                return [
                    'name' => $specialty,
                    'total' => $appointment ? $appointment->total : 0,
                ];
            }, Specialty::values());

            return [
                'day' => $day,
                'specialties' => $specialties,
            ];
        })->values();

        return jsonResponse(
            data: [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'appointments' => [
                    'total' => $totalAppointments,
                    'students' => $totalStudents,
                    'noStudents' => $totalNoStudents,
                    'specialties' => $specialties,
                ],
                'days' => $days
            ]
        );
    }

    private function appointmentsQuery(string $startDate, string $endDate): Builder
    {
        return Appointment::query()
            ->selectRaw("
            doctors.specialty,
            COUNT(CASE WHEN patients.professional_school IS NOT NULL THEN 1 END) AS total_students,
            COUNT(CASE WHEN patients.professional_school IS NULL THEN 1 END) AS total_no_students,
            COUNT(appointments.id) AS total_appointments
        ")
            ->join('doctors', 'doctors.user_id', '=', 'appointments.doctor_id')
            ->join('patients', 'patients.user_id', '=', 'appointments.patient_id')
            ->whereBetween('appointments.appointment_date', [$startDate, $endDate])
            ->groupBy('doctors.specialty');
    }

    private function daysQuery(string $startDate, string $endDate): Builder
    {
        return Appointment::query()
            ->selectRaw("
            DATE(appointments.appointment_date) AS day,
            doctors.specialty AS specialty,
            COUNT(appointments.id) as total
        ")
            ->join('doctors', 'doctors.user_id', '=', 'appointments.doctor_id')
            ->join('patients', 'patients.user_id', '=', 'appointments.patient_id')
            ->whereBetween('appointments.appointment_date', [$startDate, $endDate])
            ->groupBy('day', 'specialty')
            ->orderBy('day');
    }
}
