<?php
namespace App\Http\Controllers;

use App\Services\AuthService;

class AuthController {
    protected $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $result = $this->authService->attemptLogin($username, $password);

        if ($result['success']) {
            $user = $result['user'];
            header("Location: ../view.php?id=" . urlencode($user['id']));
            exit;
        } else {
            header('Location: login.php?error=' . urlencode($result['error']));
            exit;
        }
    }

    public function logout() {
        $this->authService->logout();
        header("Location: login.php");
        exit;
    }
}