<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'password',
    ];

    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'message' => 'Not found.',
            ], 404);
        }

        return parent::render($request, $e);
    }
}
