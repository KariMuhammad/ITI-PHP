<?php
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../app/Repositories/UserRepository.php';
require_once __DIR__ . '/../app/Decorators/LoggingUserRepository.php';
require_once __DIR__ . '/../app/Services/AuthService.php';
require_once __DIR__ . '/../app/Http/Controllers/AuthController.php';

use App\Repositories\UserRepository;
use App\Decorators\LoggingUserRepository;
use App\Services\AuthService;
use App\Http\Controllers\AuthController;

$baseRepository = new UserRepository();
$repository = new LoggingUserRepository($baseRepository);
$authService = new AuthService($repository);
$controller = new AuthController($authService);
$controller->login();
