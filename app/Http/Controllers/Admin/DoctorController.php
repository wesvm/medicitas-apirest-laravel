<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDoctorRequest;
use App\Http\Requests\Admin\UpdateDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $perPage = $request->query('perPage', 10);
        $search = $request->query('search', '');

        $doctors = User::active()->byRole(Roles::DOCTOR)
            ->with(['doctor.schedule'])
            ->search($search)
            ->sort()
            ->paginate($perPage, ['*'], 'page', $page);

        return jsonResponse(data: [
            'users' => DoctorResource::collection($doctors),
            'pagination' => [
                'total' => $doctors->total(),
                'current_page' => $doctors->currentPage(),
                'per_page' => $doctors->perPage(),
                'last_page' => $doctors->lastPage(),
                'next_page_url' => $doctors->nextPageUrl(),
                'prev_page_url' => $doctors->previousPageUrl(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request)
    {
        return transactional(function () use ($request) {

            $user = User::create([
                'first_name' => $request->get('firstName'),
                'last_name' => $request->get('lastName'),
                'phone' => $request->get('phone') ?? null,
                'dni' => $request->get('dni'),
                'email' => $request->get('email'),
                'password' => bcrypt($request->get('dni')),
                'role' => Roles::DOCTOR->value,
            ]);

            Doctor::create([
              'user_id' => $user->id,
              'schedule_id' => $request->get('scheduleId'),
              'specialty' => $request->get('specialty'),
            ]);

            return jsonResponse(
                status: 201,
                message: 'Doctor created successfully!',
                data: ['user' => new DoctorResource($user)]
            );
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $doctor = User::active()->byRole(Roles::DOCTOR)
            ->with('doctor.schedule')
            ->findOrFail($id);
        return jsonResponse(data: ['user' => new DoctorResource($doctor)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorRequest $request, User $doctor)
    {
        return transactional(function () use ($request, $doctor) {
            $doctor->first_name = $request->get('firstName');
            $doctor->last_name = $request->get('lastName');
            $doctor->phone = $request->get('phone');
            $doctor->dni = $request->get('dni');
            $doctor->email = $request->get('email');

            $doctor->doctor->schedule_id = $request->get('scheduleId');
            $doctor->doctor->specialty = $request->get('specialty');

            if ($doctor->isDirty() || $doctor->doctor->isDirty()) {
                $doctor->save();
                $doctor->doctor->save();

                return jsonResponse(
                    message: 'Doctor updated successfully',
                    data: ['user' => new DoctorResource($doctor)]
                );
            }

            return jsonResponse(
                message: 'No changes detected',
                data: ['user'=> new DoctorResource($doctor)]
            );
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        $doctor->user->update(['is_active' => false]);

        return jsonResponse(
            message: 'Doctor deactivated successfully.',
        );
    }
}
