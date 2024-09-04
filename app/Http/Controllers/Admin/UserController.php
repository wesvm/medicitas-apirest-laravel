<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $perPage = $request->query('perPage', 10);
        $search = $request->query('search', '');
        $role = $request->query('role', '');
        $active = $request->query('active');

        $query = User::search($search)->sort();

        if ($active !== null) {
            $query->where('is_active', $active);
        }

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->paginate($perPage, ['*'], 'page', $page);

        return jsonResponse(data: [
            'users' => UserResource::collection($users),
            'pagination' => [
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'last_page' => $users->lastPage(),
                'next_page_url' => $users->nextPageUrl(),
                'prev_page_url' => $users->previousPageUrl(),
            ]
        ]);
    }

    public function show(User $user)
    {
        return jsonResponse(data: ['user' => new UserResource($user)]);
    }

    public function update(UserRequest $request, User $user)
    {
        return transactional(function () use ($request, $user) {
            $user->first_name = $request->get('firstName');
            $user->last_name = $request->get('lastName');
            $user->phone = $request->get('phone');
            $user->dni = $request->get('dni');
            $user->email = $request->get('email');
            $user->is_active = $request->get('active');

            if ($user->isDirty()) {
                $user->save();
                return jsonResponse(
                    message: 'User updated successfully',
                    data: ['user' => new UserResource($user)]
                );
            }

            return jsonResponse(
                message: 'No changes detected',
                data: ['user' => new UserResource($user)]
            );
        });
    }
}
