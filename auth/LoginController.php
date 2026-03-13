<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Repositories\UserRepository;
use App\Decorators\LoggingUserRepository;
use App\Services\AuthService;
use App\Http\Controllers\AuthController;

$baseRepository = new UserRepository();
$repository = new LoggingUserRepository($baseRepository);
$authService = new AuthService($repository);
$controller = new AuthController($authService);
$controller->login();
