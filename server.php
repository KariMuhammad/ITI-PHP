<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Repositories\UserRepository;
use App\Decorators\LoggingUserRepository;
use App\Http\Controllers\UserController;

$baseRepository = new UserRepository();
$repository = new LoggingUserRepository($baseRepository);

$controller = new UserController($repository);
$controller->handleRequest();
