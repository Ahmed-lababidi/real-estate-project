<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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

        Scramble::afterOpenApiGenerated(function ($openApi) {
            // Add custom metadata to the OpenAPI documentation
            $openApi->secure(
                SecurityScheme::http('bearer')
            );
        });

        RateLimiter::for('public-api', function (Request $request) {
            return [
                Limit::perMinute(60)->by($request->ip()),
            ];
        });

        RateLimiter::for('reservation-api', function (Request $request) {
            return [
                Limit::perMinute(10)->by($request->ip()),
            ];
        });

        RateLimiter::for('contact-api', function (Request $request) {
            return [
                Limit::perMinute(10)->by($request->ip()),
            ];
        });
    }
}
