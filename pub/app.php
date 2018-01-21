<?php

require_once __DIR__.'/../vendor/autoload.php';

$bootstrapFilePath = __DIR__.'/../app/bootstrap.php';

set_time_limit(0);

$app = require $bootstrapFilePath;

$app->run();
