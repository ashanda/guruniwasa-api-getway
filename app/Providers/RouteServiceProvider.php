<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $this->configureRateLimiting($router);

        // Define API routes
        $router->group([
            'namespace' => 'App\Http\Controllers',
            'prefix' => 'api/' . env('API_VERSION', 'v1'),
        ], function () use ($router) {
            require base_path('routes/api.php');
        });

        // Define web routes
        $router->group([
            'namespace' => 'App\Http\Controllers',
        ], function () use ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @param Router $router
     * @return void
     */
    protected function configureRateLimiting(Router $router)
    {
        $router->get('/', function () use ($router) {
            return $router->app->version();
        });
    }
}
