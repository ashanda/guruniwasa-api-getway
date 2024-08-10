<?php
use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;
use Prometheus\RenderTextFormat;
/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/metrics', function () {
    // Create the CollectorRegistry instance with an InMemory storage adapter
    $adapter = new InMemory();
    $registry = new CollectorRegistry($adapter);

    // Register some metrics
    $counter = $registry->getOrRegisterCounter('my_namespace', 'my_custom_metric', 'My custom counter', ['type']);
    $counter->inc(['example']);
    
    $gauge = $registry->getOrRegisterGauge('my_namespace', 'my_gauge_metric', 'My gauge metric');
    $gauge->set(42);

    $histogram = $registry->getOrRegisterHistogram('my_namespace', 'my_timing_metric', 'My timing metric', ['type']);
    $histogram->observe(350, ['example']);

    // Render the metrics to the Prometheus text format
    $renderer = new RenderTextFormat();
    $result = $renderer->render($registry->getMetricFamilySamples());

    return response($result, 200, ['Content-Type' => 'text/plain']);
});
