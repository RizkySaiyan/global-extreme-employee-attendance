<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(using: function () {
        $namespace = 'App\\Http\\Controllers';

        $version = config('base.conf.version');
        $service = config('base.conf.service');

        Route::match(['get', 'post'], 'testing', "$namespace\\Controller@testing");

        Route::prefix(config('base.conf.prefix.web') . "/$version/$service")
            ->middleware(['web'])
            ->namespace("$namespace\\" . config('base.conf.namespace.web'))
            ->group(base_path('routes/web.php'));

        Route::prefix(config('base.conf.prefix.mobile') . "/$version/$service")
            ->middleware(['web'])
            ->namespace("$namespace\\" . config('base.conf.namespace.mobile'))
            ->group(base_path('routes/mobile.php'));

        Route::prefix(config('base.conf.prefix.mygx') . "/$version/$service")
            ->middleware(['web'])
            ->namespace("$namespace\\" . config('base.conf.namespace.mygx'))
            ->group(base_path('routes/mygx.php'));
    })
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->validateCsrfTokens(['api/*']);
        $middleware->alias([
            'role' => RoleMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->withSchedule(function () {
        Schedule::command('app:update-resign-command')->dailyAt('00:00');
        Schedule::command('app:set-weekly-day-off-command')->yearlyOn(12, 1, '23:00');
        // Schedule::command('app:test-cron-server-command')->everyMinute();
    })->create();
