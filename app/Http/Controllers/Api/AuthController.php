<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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

        $user->tokens()->delete();
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
     * Enviar enlace para recuperar contraseña
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiResponse::error('Este correo no está registrado en el sistema.', 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return ApiResponse::success(
                null,
                'Se ha enviado un enlace a tu correo para restablecer tu contraseña. Revisa tu bandeja de entrada y la carpeta de spam.'
            );
        }

        return ApiResponse::error('No se pudo enviar el enlace. Intenta de nuevo más tarde.', 422);
    }

    /**
     * Restablecer contraseña con el token recibido por correo
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'raw_password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return ApiResponse::success(null, 'Contraseña restablecida correctamente.');
        }

        return ApiResponse::error('Token inválido o expirado. Solicita un nuevo enlace.', 422);
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

        $data['workstation'] = $request->input('workstation', '') ?? '';

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
