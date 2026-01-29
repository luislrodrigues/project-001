<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (!auth()->user()->can('consult user')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $query = User::with(['company', 'roles']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('company_id') && $request->company_id !== '') {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate($request->get('per_page', 10));

        return ApiResponse::success(UserResource::collection($users));
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'raw_password' => $request->password,
                'workstation' => $request->workstation,
                'company_id' => $request->company_id,
                'status' => 'active',
            ]);

            // Asignar rol si se proporciona
            if ($request->has('role_id') && $request->role_id) {
                $role = Role::find($request->role_id);
                if ($role) {
                    $user->assignRole($role->name);
                }
            }

            return ApiResponse::success(
                new UserResource($user->load(['company', 'roles'])),
                'Usuario creado correctamente',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Error al crear el usuario', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function show(User $user): JsonResponse
    {
        if (!auth()->user()->can('consult user')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        return ApiResponse::success(new UserResource($user->load(['company', 'roles'])));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'workstation' => $request->workstation,
                'company_id' => $request->company_id,
            ];

            if ($request->has('password') && $request->password) {
                $data['password'] = bcrypt($request->password);
                $data['raw_password'] = $request->password;
            }

            $user->update($data);

            if ($request->has('role_id')) {
                if ($request->role_id) {
                    $role = Role::find($request->role_id);
                    if ($role) {
                        $user->syncRoles([$role->name]);
                    }
                } else {
                    $user->syncRoles([]);
                }
            }

            return ApiResponse::success(
                new UserResource($user->load(['company', 'roles'])),
                'Usuario actualizado correctamente'
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el usuario', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function destroy(User $user): JsonResponse
    {
        if (!auth()->user()->can('delete user')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        try {
            $user->delete();

            return ApiResponse::success(null, 'Usuario eliminado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error('Error al eliminar el usuario', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function toggleStatus(User $user): JsonResponse
    {
        if (!auth()->user()->can('inactive user')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        try {
            $user->status = ($user->status === 'active') ? 'inactive' : 'active';
            $user->save();

            return ApiResponse::success(
                new UserResource($user->load(['company', 'roles'])),
                'Estado del usuario actualizado correctamente'
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el estado del usuario', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function getUsers(): JsonResponse
    {
        $users = User::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name', 'email')
            ->get();

        return ApiResponse::success($users);
    }

    public function getRoles(): JsonResponse
    {
        $roles = Role::where('guard_name', 'web')
            ->select('id', 'name')
            ->get();

        return ApiResponse::success($roles);
    }
}
