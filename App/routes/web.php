<?php
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\UserController;

$router = new Router();
$router->get('/volunteer-managment/public/auth/login', [AuthController::class, 'showLoginForm']);
$router->post('/volunteer-managment/public/auth/login', [AuthController::class, 'login']);

$router->get('/volunteer-managment/public/auth/register', [AuthController::class, 'showRegisterForm']);
$router->post('/volunteer-managment/public/auth/register', [AuthController::class, 'register']);
$router->post('/volunteer-managment/public/auth/logout', [AuthController::class, 'logout']);

$router->get('/volunteer-managment/public/users', [UserController::class, 'index']);
// $router->post('/volunteer-managment/public/users', [AuthController::class, 'register']);
// $router->get('/volunteer-managment/public/users', [AuthController::class, 'register']);

