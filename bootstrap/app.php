<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function(Throwable $e){
            /*
             * If the requested method is not allowed for that specific route, redirect to dashboard
             */
           if($e instanceof MethodNotAllowedHttpException){
               return redirect()->route('errors.405');
           }

           /*
            * If the requested route is not found, redirect to dashboard
            */
           if($e instanceof NotFoundHttpException){
               return redirect()->route('errors.404');
           }
        });
    })
    ->create();
