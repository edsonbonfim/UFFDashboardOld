<?php

include getcwd() . '/vendor/autoload.php';

use Bonfim\Application\App;

$app = new App();

$app->register(Bonfim\Component\View\View::class);
$app->register(Bonfim\Component\Routing\Route::class);

$app->handle();
