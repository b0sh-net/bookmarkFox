<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

require __DIR__ . '/../vendor/autoload.php';

$app = new Application(dirname(__DIR__));

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Request::capture());
$response->send();
$kernel->terminate($request, $response);
