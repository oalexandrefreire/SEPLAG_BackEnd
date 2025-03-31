<?php

namespace App\Providers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->afterResolving(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            function ($handler) {
                $handler->renderable(function (AuthenticationException $e, Request $request) {
                    return response()->json([
                        'message' => 'Token de autenticação não informado ou inválido.'
                    ], 401);
                });
            }
        );
    }
}
