<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
    * The path to the "home" route for your application.
    * Typically, users are redirected here after authentication.
    */
    public const HOME = '/dashboard';


    /**
    * Define your route model bindings, pattern filters, and routes.
    */
    public function boot(): void
    {
        $this->configureRateLimiting();


        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }


    /**
    * Configure the rate limiters for the application.
    */
    protected function configureRateLimiting(): void
    {
        /*
        |------------------------------------------------------------------
        | Login API
        |------------------------------------------------------------------
        | 5 requests / minute
        | Key: IP + email
        */
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(
                $request->ip() . '|' . strtolower((string) $request->input('email'))
            );
        });

        /*
        |------------------------------------------------------------------
        | Register API
        |------------------------------------------------------------------
        | 3 requests / minute
        | Key: IP
        */
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        /*
        |------------------------------------------------------------------
        | Authenticated API (Sanctum)
        |------------------------------------------------------------------
        | 60 requests / minute
        | Key: user_id (fallback IP)
        */
        RateLimiter::for('api-auth', function (Request $request) {
            return Limit::perMinute(60)->by(
                optional($request->user())->id ?: $request->ip()
            );
        });
    }
}
