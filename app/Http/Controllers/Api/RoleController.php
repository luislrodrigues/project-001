<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Listar roles (para gestión de permisos). Solo roles del guard web.
     */
    public function index(): JsonResponse
    {
        if (!auth()->user()->can('consult role')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $roles = Role::where('guard_name', 'web')
            ->withCount('permissions')
            ->orderByRaw("CASE WHEN name = 'Administrador' THEN 1 WHEN name = 'Supervisor' THEN 2 WHEN name = 'Cliente' THEN 3 ELSE 4 END, name")
            ->get()
            ->map(fn (Role $role) => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions_count' => $role->permissions_count,
            ]);

        return ApiResponse::success($roles);
    }

    /**
     * Crear un nuevo rol.
     */
    public function store(Request $request): JsonResponse
    {
        if (!auth()->user()->can('save role')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
            'permissions.*.exists' => 'Uno o más permisos no son válidos.',
        ]);

        $name = trim($request->input('name'));
        if (Role::where('guard_name', 'web')->where('name', $name)->exists()) {
            return ApiResponse::error('Ya existe un rol con ese nombre.', 422);
        }

        $role = Role::create(['name' => $name, 'guard_name' => 'web']);
        $role->syncPermissions($request->input('permissions', []));

        $role->loadCount('permissions');
        $role->load('permissions');

        return ApiResponse::success([
            'id' => $role->id,
            'name' => $role->name,
            'permissions_count' => $role->permissions_count,
            'permissions' => $role->permissions->pluck('name')->all(),
        ], 'Rol creado correctamente.', 201);
    }

    /**
     * Ver un rol con sus permisos.
     */
    public function show(int $id): JsonResponse
    {
        if (!auth()->user()->can('consult role')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $role = Role::where('guard_name', 'web')->find($id);
        if (!$role) {
            return ApiResponse::notFound('Rol no encontrado.');
        }

        $role->load('permissions');
        $permissions = $role->permissions->pluck('name')->all();

        return ApiResponse::success([
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Listar todos los permisos (para el formulario de asignación).
     */
    public function permissions(): JsonResponse
    {
        if (!auth()->user()->can('consult role')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Permission $p) => ['id' => $p->id, 'name' => $p->name]);

        return ApiResponse::success($permissions);
    }

    /**
     * Actualizar nombre y permisos de un rol.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        if (!auth()->user()->can('edit role')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $role = Role::where('guard_name', 'web')->find($id);
        if (!$role) {
            return ApiResponse::notFound('Rol no encontrado.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
            'permissions.*.exists' => 'Uno o más permisos no son válidos.',
        ]);

        $name = trim($request->input('name'));
        if (Role::where('guard_name', 'web')->where('name', $name)->where('id', '!=', $id)->exists()) {
            return ApiResponse::error('Ya existe un rol con ese nombre.', 422);
        }

        $role->update(['name' => $name]);
        $role->syncPermissions($request->input('permissions', []));

        $role->load('permissions');
        $permissions = $role->permissions->pluck('name')->all();

        return ApiResponse::success([
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $permissions,
        ], 'Rol actualizado correctamente.');
    }
}
