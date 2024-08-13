<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;

class AdminController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Admin::class);

        return AdminResource::collection(Admin::all());
    }

    public function store(AdminRequest $request)
    {
        $this->authorize('create', Admin::class);

        return new AdminResource(Admin::create($request->validated()));
    }

    public function show(Admin $admin)
    {
        $this->authorize('view', $admin);

        return new AdminResource($admin);
    }

    public function update(AdminRequest $request, Admin $admin)
    {
        $this->authorize('update', $admin);

        $admin->update($request->validated());

        return new AdminResource($admin);
    }

    public function destroy(Admin $admin)
    {
        $this->authorize('delete', $admin);

        $admin->delete();

        return response()->json();
    }
}
