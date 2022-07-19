<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Carbon\Carbon;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        // convert "date" into carbon date
        $this->bind('date', function ($date) {
            $carbon_date = Carbon::createFromFormat('Y-m-d', $date);
            if ($carbon_date->format('Y-m-d') === $date) return $carbon_date;
            throw new NotFoundHttpException();
        });

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));

        Route::prefix('report/api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/report.api.php'));

        Route::prefix('mobile/api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/mobile.api.php'));

        Route::prefix('public/api')
            ->namespace($this->namespace)
            ->group(base_path('routes/public.api.php'));
    }
}
