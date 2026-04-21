<?php

use Appstract\Opcache\Http\Controllers\OpcacheController;

$router->get('clear', [OpcacheController::class, 'clear']);
$router->get('config', [OpcacheController::class, 'config']);
$router->get('status', [OpcacheController::class, 'status']);
$router->get('compile', [OpcacheController::class, 'compile']);
