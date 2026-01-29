<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;

/**
 * Helper para unificar la estructura de todas las respuestas JSON de la API.
 *
 * Estructura estándar:
 * - Éxito: { success: true, message?: string, data?: mixed, meta?: array }
 * - Error: { success: false, message: string, errors?: array, code?: int }
 */
class ApiResponse
{
    /**
     * Respuesta de éxito (200 por defecto, 201 para creado).
     */
    public static function success(
        mixed $data = null,
        ?string $message = null,
        int $status = 200
    ): JsonResponse {
        $payload = ['success' => true];

        if ($message !== null) {
            $payload['message'] = $message;
        }

        if ($data !== null) {
            if ($data instanceof ResourceCollection || $data instanceof JsonResource) {
                $decoded = $data->response()->getData(true);
                $payload['data'] = $decoded['data'] ?? $decoded;
                if (!empty($decoded['meta'])) {
                    $payload['meta'] = $decoded['meta'];
                    // Compatibilidad: paginación en nivel superior para el frontend
                    foreach ($decoded['meta'] as $key => $value) {
                        $payload[$key] = $value;
                    }
                }
                if (!empty($decoded['links'])) {
                    $payload['links'] = $decoded['links'];
                    $payload['first_page_url'] = $decoded['links']['first'] ?? null;
                    $payload['last_page_url'] = $decoded['links']['last'] ?? null;
                    $payload['prev_page_url'] = $decoded['links']['prev'] ?? null;
                    $payload['next_page_url'] = $decoded['links']['next'] ?? null;
                }
            } elseif ($data instanceof AbstractPaginator || $data instanceof AbstractCursorPaginator) {
                $payload['data'] = $data->items();
                $payload['meta'] = [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ];
            } elseif ($data instanceof Collection) {
                $payload['data'] = $data->all();
            } else {
                $payload['data'] = $data;
            }
        }

        return response()->json(array_filter($payload, fn ($v) => $v !== null), $status);
    }

    /**
     * Respuesta de error (4xx/5xx).
     */
    public static function error(
        string $message,
        int $status = 500,
        ?array $errors = null
    ): JsonResponse {
        $payload = [
            'success' => false,
            'message' => $message,
            'code' => $status,
        ];

        if ($errors !== null && $errors !== []) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    /**
     * Respuesta de error de validación (422).
     * Acepta MessageBag o array de errores.
     */
    public static function validationError($errors, string $message = 'Los datos enviados no son válidos.'): JsonResponse
    {
        $list = $errors instanceof \Illuminate\Support\MessageBag
            ? $errors->toArray()
            : (is_array($errors) ? $errors : []);

        return self::error($message, 422, $list);
    }

    /**
     * Respuesta 404.
     */
    public static function notFound(?string $message = null): JsonResponse
    {
        return self::error($message ?? 'Recurso no encontrado.', 404);
    }

    /**
     * Respuesta 403.
     */
    public static function forbidden(?string $message = null): JsonResponse
    {
        return self::error($message ?? 'No tienes los permisos requeridos.', 403);
    }
}
