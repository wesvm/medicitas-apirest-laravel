<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $perPage = 10;
        $page = $request->query('page', 1);

        $admins = User::active()->byRole(Roles::ADMIN)
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return jsonResponse(data: [
            'users' => AdminResource::collection($admins),
            'pagination' => [
                'total' => $admins->total(),
                'current_page' => $admins->currentPage(),
                'per_page' => $admins->perPage(),
                'last_page' => $admins->lastPage(),
                'next_page_url' => $admins->nextPageUrl(),
                'prev_page_url' => $admins->previousPageUrl(),
            ]
        ]);
    }

    public function store(UserRequest $request)
    {
        return transactional(function () use ($request) {
            $user = User::create([
                'first_name' => $request->get('firstName'),
                'last_name' => $request->get('lastName'),
                'phone' => $request->get('phone') ?? null,
                'dni' => $request->get('dni'),
                'email' => $request->get('email'),
                'password' => bcrypt($request->get('dni')),
                'role' => Roles::ADMIN->value,
            ]);

            Admin::create([
                'user_id' => $user->id
            ]);

            return jsonResponse(
                status: 201,
                message: 'Admin created successfully!',
                data: ['user' => new AdminResource($user)]
            );
        });
    }

    public function show(User $admin)
    {
        return jsonResponse(data: ['user' => new AdminResource($admin)]);
    }

    public function update(UserRequest $request, User $admin)
    {
        return transactional(function () use ($request, $admin) {
            $admin->first_name = $request->get('firstName');
            $admin->last_name = $request->get('lastName');
            $admin->phone = $request->get('phone');
            $admin->dni = $request->get('dni');
            $admin->email = $request->get('email');

            if($admin->isDirty()){
                $admin->save();
                return jsonResponse(
                    message: 'Admin updated successfully',
                    data: ['user' => new AdminResource($admin)]
                );
            }

            return jsonResponse(
                message: 'No changes detected',
                data: ['user'=> new AdminResource($admin)]
            );
        });
    }

    public function destroy(Admin $admin)
    {
        $admin->user->update(['is_active' => false]);

        return jsonResponse(
            message: 'Admin deactivated successfully.',
        );
    }
}
