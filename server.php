<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/app/Repositories/UserRepository.php';
require_once __DIR__ . '/app/Decorators/LoggingUserRepository.php';
require_once __DIR__ . '/app/Http/Controllers/UserController.php';

use App\Repositories\UserRepository;
use App\Decorators\LoggingUserRepository;
use App\Http\Controllers\UserController;

$baseRepository = new UserRepository();
$repository = new LoggingUserRepository($baseRepository);

$controller = new UserController($repository);
$controller->handleRequest();
