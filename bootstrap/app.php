<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Register custom middleware aliases
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'outlet.access' => \App\Http\Middleware\CheckOutletAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle authentication exceptions
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login'));
        });
        
        // Handle other exceptions gracefully
        $exceptions->render(function (\Throwable $e, $request) {
            // If error contains "Call to a member function" and user is not authenticated
            if (str_contains($e->getMessage(), 'Call to a member function') && !auth()->check()) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Session expired. Please login again.'], 401);
                }
                return redirect()->route('login')->with('error', 'Session Anda telah berakhir. Silakan login kembali.');
            }
            
            // Let other exceptions be handled normally
            return null;
        });
    })->create();
