<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success(string $message = 'Success', mixed $data = null, int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    public static function error(string $message, int $status = 400, mixed $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    public static function notFound(string $message = 'Resource not found')
    {
        return self::error($message, 404);
    }

    public static function paginated($paginator, string $message = 'Success')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $paginator->items(),

            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],

            'links' => [
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],

        ], 200);
    }

    public static function forbidden(string $message = 'Forbidden')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }
}
