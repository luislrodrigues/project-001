<?php

namespace App\Exceptions;

use App\Http\Helpers\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Respuesta JSON para errores de validación.
     */
    protected function invalidJson($request, ValidationException $exception): \Illuminate\Http\JsonResponse
    {
        return ApiResponse::validationError(
            $exception->errors(),
            $exception->getMessage()
        );
    }

    /**
     * Las rutas de API siempre responden JSON: sin esto, un request sin "Accept: application/json"
     * intenta redirigir a la ruta "login", que en esta SPA no existe, y termina en un error 500.
     */
    protected function shouldReturnJson($request, Throwable $e)
    {
        return $request->is('api/*') || parent::shouldReturnJson($request, $e);
    }
}
