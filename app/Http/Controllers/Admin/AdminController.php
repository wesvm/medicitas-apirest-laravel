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
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'phone' => $request->phone ?? null,
                'dni' => $request->dni,
                'email' => $request->email,
                'password' => bcrypt($request->dni),
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

    public function update($request, Admin $admin)
    {
        // TODO: HERE
    }

    public function destroy(Admin $admin)
    {
        $admin->user->update(['is_active' => false]);

        return jsonResponse(
            message: 'Admin deactivated successfully.',
        );
    }
}
