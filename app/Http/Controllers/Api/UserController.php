<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        if (!auth()->user()->can('consult user')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $query = User::with(['company', 'roles']);

        // Filtros
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

        return UserResource::collection($users);
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

            return response()->json([
                'message' => 'Usuario creado correctamente',
                'data' => new UserResource($user->load(['company', 'roles']))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(User $user): JsonResponse
    {
        if (!auth()->user()->can('consult user')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        return response()->json([
            'data' => new UserResource($user->load(['company', 'roles']))
        ]);
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

            // Actualizar contraseña solo si se proporciona
            if ($request->has('password') && $request->password) {
                $data['password'] = bcrypt($request->password);
                $data['raw_password'] = $request->password;
            }

            $user->update($data);

            // Sincronizar rol si se proporciona
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

            return response()->json([
                'message' => 'Usuario actualizado correctamente',
                'data' => new UserResource($user->load(['company', 'roles']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user): JsonResponse
    {
        if (!auth()->user()->can('delete user')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        try {
            $user->delete();

            return response()->json([
                'message' => 'Usuario eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage()
            ], 500);
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

            return response()->json([
                'message' => 'Estado del usuario actualizado correctamente',
                'data' => new UserResource($user->load(['company', 'roles']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el estado del usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUsers(): JsonResponse
    {
        $users = User::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name', 'email')
            ->get();

        return response()->json(['data' => $users]);
    }

    public function getRoles(): JsonResponse
    {
        $roles = Role::where('guard_name', 'web')
            ->select('id', 'name')
            ->get();

        return response()->json(['data' => $roles]);
    }
}
