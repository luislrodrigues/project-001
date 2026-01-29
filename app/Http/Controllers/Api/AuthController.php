<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Iniciar sesión
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no coinciden con nuestros registros.'],
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Tu cuenta no está activa.'],
            ]);
        }

        // Revocar tokens anteriores
        $user->tokens()->delete();

        // Crear nuevo token
        $token = $user->createToken('api-token')->plainTextToken;

        $userData = $user->load('company');
        
        return ApiResponse::success([
            'user' => [
                'id' => $userData->id,
                'name' => $userData->name,
                'email' => $userData->email,
                'company' => $userData->company,
            ],
            'token' => $token,
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'roles' => $user->getRoleNames()->toArray(),
        ]);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Sesión cerrada correctamente');
    }

    /**
     * Obtener información del usuario autenticado
     */
    public function user(Request $request)
    {
        $user = $request->user()->load('company');
        return ApiResponse::success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'workstation' => $user->workstation,
            'area' => $user->area,
            'company' => $user->company ? [
                'id' => $user->company->id,
                'name' => $user->company->name,
            ] : null,
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'roles' => $user->getRoleNames()->toArray(),
            'profile_photo_url' => $user->profile_photo_url,
        ]);
    }

    /**
     * Actualizar perfil del usuario autenticado
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->has('workstation')) {
            $data['workstation'] = $request->workstation;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $user->load('company');

        return ApiResponse::success(
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'workstation' => $user->workstation,
                'area' => $user->area,
                'company' => $user->company ? [
                    'id' => $user->company->id,
                    'name' => $user->company->name,
                ] : null,
                'profile_photo_url' => $user->profile_photo_url,
            ],
            'Perfil actualizado correctamente'
        );
    }
}
