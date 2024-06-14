<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/api_v1.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception) {
            foreach ($exception->errors() as $key => $value) {
                foreach ($value as $message) {
                    $errors[] = [
                        'status' => 422,
                        'message' => $message,
                        'source' => $key
                    ];
                }
            }
            return response()->json([
                'errors' => $errors
            ]);
        });

        $exceptions->render(function (ModelNotFoundException $exception) {
            $errors = [
                [
                    'status' => 404,
                    'message' => 'The resource cannot be found.',
                    'source' => $exception->getModel()
                ]
            ];
            return response()->json([
                'errors' => $errors
            ]);
        });

        $exceptions->render(function (AuthenticationException $exception) {
            $errors = [
                [
                    'status' => 404,
                    'message' => 'Unauthenticated',
                    'source' => ''
                ]
            ];
            return response()->json([
                'errors' => $errors
            ]);
        });

        $exceptions->render(function (Throwable $exception) {
            $className = get_class($exception);
            $index = strrpos($className, '\\');

            $errors = [
                [
                    'type' => substr($className, $index + 1),
                    'status' => 0,
                    'message' => $exception->getMessage(),
                    'source' => 'Line: ' . $exception->getLine() . ': ' . $exception->getFile()
                ]
            ];
            return response()->json([
                'errors' => $errors
            ]);
        });
    })->create();
