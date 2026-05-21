<?php

namespace App\Traits;

use App\Http\Responses\ApiResponse;

trait HasApiResponse
{
    protected function success($data = null, string $message = 'Success', int $code = 200)
    {
        return ApiResponse::success($data, $message, $code);
    }

    protected function error(string $message = 'Error', int $code = 400, $errors = null)
    {
        return ApiResponse::error($message, $code, $errors);
    }

    protected function validationError($errors, string $message = 'Validation Failed')
    {
        return ApiResponse::validationError($errors, $message);
    }

    protected function notFound(string $message = 'Data tidak ditemukan')
    {
        return ApiResponse::notFound($message);
    }

    protected function paginate($paginator, string $message = 'Success')
    {
        return ApiResponse::paginate($paginator, $message);
    }
    protected function unauthorized(string $message)
    {
        return ApiResponse::unauthorized($message);
    }
}

