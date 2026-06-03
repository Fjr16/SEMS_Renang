<?php

namespace App\Http\Responses;

class ApiResponse {
    /**
     * Success Response
     */
    public static function success($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'status'  => true,
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Error Response
     */
    public static function error(string $message = 'Error', int $code = 400, $errors = null)
    {
        return response()->json([
            'status'  => false,
            'code'    => $code,
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }

    /**
     * Validation Error Response
     */
    public static function validationError($errors, string $message = 'Kesalahan Validasi')
    {
        return response()->json([
            'status'  => false,
            'code'    => 422,
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }

    /**
     * Not Found Response
     */
    public static function notFound(string $message = 'Data tidak ditemukan')
    {
        return response()->json([
            'status'  => false,
            'code'    => 404,
            'message' => $message,
            'data'    => null,
        ], 404);
    }

    /**
     * Empty data
     */
    public static function empty(string $message = 'Data belum tersedia')
    {
        return response()->json([
            'status'  => false,
            'code'    => 200,
            'message' => $message,
            'data'    => [],
        ], 200);
    }

    /**
     * Unauthenticate Response
     */
    public static function unauthenticate(string $message = 'Belum login / tidak terautentikasi')
    {
        return response()->json([
            'status'  => false,
            'code'    => 401,
            'message' => $message,
            'data'    => null,
        ], 401);
    }
    /**
     * Unauthorized Response
     */
    public static function unauthorized(string $message = 'Tidak memiliki hak akses')
    {
        return response()->json([
            'status'  => false,
            'code'    => 403,
            'message' => $message,
            'data'    => null,
        ], 403);
    }

    /**
     * Paginated Response
     */
    public static function paginate($paginator, string $message = 'Success')
    {
        return response()->json([
            'status'  => true,
            'code'    => 200,
            'message' => $message,
            'data'    => $paginator->items(),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],
        ], 200);
    }
}
