<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

abstract class AppController extends Controller
{
    protected function json(mixed $data, int $status = 200, array $headers = []): JsonResponse
    {
        return response()->json($data, $status, $headers);
    }

    protected function error(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        $payload = ['message' => $message];
        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }
        return response()->json($payload, $status);
    }
}
