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
            'message' => $exception->validator->errors()->all(),
        ], Response::HTTP_BAD_REQUEST);
    }
}
