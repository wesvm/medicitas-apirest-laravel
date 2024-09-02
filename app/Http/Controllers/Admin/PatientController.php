<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePatientRequest;
use App\Http\Requests\Admin\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class PatientController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:admin,doctor');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $perPage = $request->query('perPage', 10);
        $search = $request->query('search', '');

        $patients = User::active()->byRole(Roles::PATIENT)
            ->with('patient')
            ->search($search)
            ->sort()
            ->paginate($perPage, ['*'], 'page', $page);

        return jsonResponse(data: [
            'users' => PatientResource::collection($patients),
            'pagination' => [
                'total' => $patients->total(),
                'current_page' => $patients->currentPage(),
                'per_page' => $patients->perPage(),
                'last_page' => $patients->lastPage(),
                'next_page_url' => $patients->nextPageUrl(),
                'prev_page_url' => $patients->previousPageUrl(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request)
    {
        return transactional(function () use ($request) {
            $user = User::create([
                'first_name' => $request->get('firstName'),
                'last_name' => $request->get('lastName'),
                'phone' => $request->get('phone') ?? null,
                'dni' => $request->get('dni'),
                'email' => $request->get('email'),
                'password' => bcrypt($request->get('dni')),
                'role' => Roles::PATIENT->value,
            ]);

            Patient::create([
                'user_id' => $user->id,
                'date_of_birth' => $request->get('dateOfBirth'),
                'place_of_birth' => $request->get('placeOfBirth') ?? null,
                'address' => $request->get('address'),
                'professional_school' => $request->get('professionalSchool') ?? null,
                'occupation' => $request->get('occupation') ?? null,
                'insurance_type' => $request->get('insuranceType'),
                'emergency_contact_phone' => $request->get('emergencyContactPhone') ?? null,
            ]);

            return jsonResponse(
                status: 201,
                message: 'Patient created successfully!',
                data: ['user' => new PatientResource($user)]
            );
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $patient = User::active()->byRole(Roles::PATIENT)
            ->with('patient')
            ->findOrFail($id);
        return jsonResponse(data: ['user' => new PatientResource($patient)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, User $patient)
    {
        return transactional(function () use ($request, $patient) {

            $patient->first_name = $request->get('firstName');
            $patient->last_name = $request->get('lastName');
            $patient->phone = $request->get('phone');
            $patient->dni = $request->get('dni');
            $patient->email = $request->get('email');

            $patient->patient->date_of_birth = $request->get('dateOfBirth');
            $patient->patient->place_of_birth = $request->get('placeOfBirth');
            $patient->patient->professional_school = $request->get('professionalSchool');
            $patient->patient->address = $request->get('address');
            $patient->patient->occupation = $request->get('occupation');
            $patient->patient->insurance_type = $request->get('insuranceType');
            $patient->patient->emergency_contact_phone = $request->get('emergencyContactPhone');

            if ($patient->isDirty() || $patient->patient->isDirty()) {
                $patient->save();
                $patient->patient->save();

                return jsonResponse(
                    message: 'Patient updated successfully',
                    data: ['user' => new PatientResource($patient)]
                );
            }

            return jsonResponse(
                message: 'No changes detected',
                data: ['user'=> new PatientResource($patient)]
            );
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->user->update(['is_active' => false]);

        return jsonResponse(
            message: 'Patient deactivated successfully.',
        );
    }
}
