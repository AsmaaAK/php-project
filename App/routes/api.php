<?php
use App\Core\RouterAPI;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\VolunteerController;

$routerApi = new RouterAPI();
$routerApi->post('/php-project/public/api/login', [AuthController::class, 'apiLogin'], false);
$routerApi->post('/php-project/public/api/register', [AuthController::class, 'apiRegister'], false);
$routerApi->get('/php-project/public/api/users', [UserController::class, 'getAllUsers'], true);
$routerApi->get('/php-project/public/api/volunteers', [VolunteerController::class, 'indexApi'],false);
$routerApi->get('/php-project/public/api/volunteers/{id}', [VolunteerController::class, 'show'],false);
$routerApi->post('/php-project/public/api/volunteers', [VolunteerController::class, 'apiCreate'], false);
$routerApi->put('/php-project/public/api/volunteers/{id}', [VolunteerController::class, 'apiupdate'], false);
$routerApi->delete('/php-project/public/api/volunteers/{id}', [VolunteerController::class, 'apiDelete'], false);


$routerApi->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
