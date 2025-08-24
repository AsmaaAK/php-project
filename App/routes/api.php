<?php
use App\Core\RouterAPI;
use App\Controllers\AuthController;
use App\Controllers\UserController;

$routerApi = new RouterAPI();
$routerApi->post('/capstone4-mvc/public/api/login', [AuthController::class, 'apiLogin'], false);
$routerApi->post('/capstone4-mvc/public/api/register', [AuthController::class, 'apiRegister'], true);
$routerApi->get('/capstone4-mvc/public/api/users', [UserController::class, 'getAllUsers'], true);

$routerApi->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
