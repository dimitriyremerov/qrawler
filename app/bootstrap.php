<?php

use Knp\Provider\ConsoleServiceProvider;
use Qrawler\ServiceProvider\FileFetcherServiceProvider;
use Silex\Application;

require_once __DIR__.'/../vendor/autoload.php';
// require_once __DIR__.'/conf/propel.php';

$app = new Application();

$app->register(new ConsoleServiceProvider());
$app->register(new FileFetcherServiceProvider());

return $app;

