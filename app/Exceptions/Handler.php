<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionsHandler;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

class Handler extends ExceptionsHandler
{
    public function register(): void
    {
        $this->renderable(function (InvalidArgumentException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }
        }); 

        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Internal Server Error',
                ], 500);
            }
        });
    }
}
