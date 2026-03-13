<?php
require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/../app/Services/AuthService.php';

use App\Services\AuthService;

$authService = new AuthService(getUserRepository());
$authService->logout();

header("Location: login.php");
exit;
