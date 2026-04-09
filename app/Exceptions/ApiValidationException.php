<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ApiValidationException extends Exception
{
    public static function render(ValidationException $exception): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Errores de validación',
            'errors' => $exception->validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
