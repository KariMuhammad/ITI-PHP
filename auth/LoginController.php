<?php
session_start();
include_once '../utils.php';

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    header('Location: login.php?error=' . urlencode('Please fill in all fields.'));
    exit;
}

// Check user in database by username
$user = getUserByUsername($username);

// For this lab we store plain password, so compare directly
if ($user && $user['password'] === $password) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['fullname'] = $user['firstname'] . ' ' . $user['lastname'];

    header("Location: ../view.php?id=" . urlencode($user['id']));
    exit;
}

// If we reach here, credentials are wrong
header('Location: login.php?error=' . urlencode('Invalid username or password.'));
exit;