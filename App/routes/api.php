<?php
use App\Core\RouterAPI;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\VolunteerController;

$routerApi = new RouterAPI();
$routerApi->post('/php-project/public/api/login', [AuthController::class, 'apiLogin'], false);
$routerApi->post('/php-project/public/api/register', [AuthController::class, 'apiRegister'], true);
$routerApi->get('/php-project/public/api/users', [UserController::class, 'getAllUsers'], true);
$routerApi->get('/php-project/public/api/volunteers', [VolunteerController::class, 'index']);
$routerApi->get('/php-project/public/api/volunteers/{id}', [VolunteerController::class, 'show']);
$routerApi->post('/php-project/public/api/volunteers', [VolunteerController::class, 'store'], true);
$routerApi->put('/php-project/public/api/volunteers/{id}', [VolunteerController::class, 'update'], true);
$routerApi->delete('/php-project/public/api/volunteers/{id}', [VolunteerController::class, 'delete'], true);


$routerApi->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
