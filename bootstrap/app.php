<?php

use App\Domain\Invoice\Exceptions\InvoiceNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e) {
            /*
             * If the requested method is not allowed for that specific route
             */
            if ($e instanceof MethodNotAllowedHttpException) {
                return redirect()->route('errors.405');
            }

            /*
             * If the requested route is not found
             */
            if ($e instanceof NotFoundHttpException) {
                return redirect()->route('errors.404');
            }
        });

        $exceptions->stopIgnoring(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        /*
         * Routine 404s and 405s (bots, stale links) are not worth logging.
         * InvoiceNotFoundException is a NotFoundHttpException too, and it should still be logged,
         * so this is a report callback instead of dontReport(): returning false skips the default logging.
         */
        $exceptions->report(function (NotFoundHttpException|MethodNotAllowedHttpException $e) {
            return $e instanceof InvoiceNotFoundException;
        });
    })
    ->create();
