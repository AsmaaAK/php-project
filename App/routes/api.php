<?php
use App\Core\RouterAPI;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\VolunteerController;
use App\Controllers\EventController;

$routerApi = new RouterAPI();
$routerApi->post('/capstone4-mvc/public/api/login', [AuthController::class, 'apiLogin'], false);
$routerApi->post('/capstone4-mvc/public/api/register', [AuthController::class, 'apiRegister'], true);
$routerApi->get('/capstone4-mvc/public/api/users', [UserController::class, 'getAllUsers'], true);

$routerApi->get('/capstone4-mvc/public/api/volunteers', [VolunteerController::class, 'index']);
$routerApi->get('/capstone4-mvc/public/api/volunteers/{id}', [VolunteerController::class, 'show']);
$routerApi->post('/capstone4-mvc/public/api/volunteers', [VolunteerController::class, 'store'], true);
$routerApi->put('/capstone4-mvc/public/api/volunteers/{id}', [VolunteerController::class, 'update'], true);
$routerApi->delete('/capstone4-mvc/public/api/volunteers/{id}', [VolunteerController::class, 'delete'], true);

$routerApi->get('/capstone4-mvc/public/api/events', [EventController::class, 'index']);
$routerApi->get('/capstone4-mvc/public/api/events/{id}', [EventController::class, 'show']);
$routerApi->post('/capstone4-mvc/public/api/events', [EventController::class, 'store'], true);
$routerApi->put('/capstone4-mvc/public/api/events/{id}', [EventController::class, 'update'], true);
$routerApi->delete('/capstone4-mvc/public/api/events/{id}', [EventController::class, 'delete'], true);



$routerApi->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
