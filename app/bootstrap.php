<?php

use Silex\Application;
use Knp\Provider\ConsoleServiceProvider;
use Qrawler\ServiceProvider\FileFetcherServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/conf/propel.php';

$app = new Application();
$app['debug'] = true;

// Changing request type to json because we are working with SPA.
$app->before(function (\Symfony\Component\HttpFoundation\Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});


$app->register(new ConsoleServiceProvider());
$app->register(new FileFetcherServiceProvider());
$app->register(new ServiceControllerServiceProvider());

//TODO Use a service provider for automatically detecting the controller

$app['job.controller'] = function () use ($app) {
    $requestStack = $app['request_stack'];
    /* @var $requestStack \Symfony\Component\HttpFoundation\RequestStack */
    $request = $requestStack->getCurrentRequest();
    return new \Qrawler\API\JobController($request);
};

$app['result.controller'] = function () use ($app) {
    $requestStack = $app['request_stack'];
    /* @var $requestStack \Symfony\Component\HttpFoundation\RequestStack */
    $request = $requestStack->getCurrentRequest();
    return new \Qrawler\API\ResultController($request);
};

$app->mount('/api', function (\Silex\ControllerCollection $api){
    $api->get('/job/{id}', 'job.controller:get');
    $api->post('/job', 'job.controller:post');
    $api->get('/result/{id}', 'result.controller:get');
});


return $app;

