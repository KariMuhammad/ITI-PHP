<?php
namespace App\Services;

use App\Repositories\UserRepositoryInterface;

class AuthService {
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function attemptLogin($username, $password) {
        if ($username === '' || $password === '') {
            return ['success' => false, 'error' => 'Please fill in all fields.'];
        }

        $user = $this->userRepository->findByUsername($username);

        // For this lab we store plain password, so compare directly
        if ($user && $user['password'] === $password) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['firstname'] . ' ' . $user['lastname'];

            return ['success' => true, 'user' => $user];
        }

        return ['success' => false, 'error' => 'Invalid username or password.'];
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        $_SESSION = [];
    }
}