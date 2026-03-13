<?php
session_start();
require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/../app/Services/AuthService.php';

use App\Services\AuthService;

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

$authService = new AuthService(getUserRepository());
$result = $authService->attemptLogin($username, $password);

if ($result['success']) {
    $user = $result['user'];
    header("Location: ../view.php?id=" . urlencode($user['id']));
    exit;
} else {
    header('Location: login.php?error=' . urlencode($result['error']));
    exit;
}
