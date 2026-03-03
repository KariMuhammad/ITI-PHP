<?php
session_start();
include_once 'utils.php';

// ─── Handle Form Submission (POST) ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = trim($_POST['edit_id'] ?? '');
    $isEditMode = !empty($editId);
    
    if (!$isEditMode) {
        $captchaInput = trim($_POST['captcha'] ?? '');
        if ($captchaInput !== ($_SESSION['captcha'] ?? '')) {
            header('Location: index.php?error=' . urlencode('Wrong captcha try again.'));
            exit;
        }
    }

    $skills = isset($_POST['skills']) ? implode('|', $_POST['skills']) : '';
    
    // Prepare data
    $data = [
        'firstname' => trim($_POST['firstname'] ?? ''),
        'lastname' => trim($_POST['lastname'] ?? ''),
        'country' => trim($_POST['country'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'gender' => trim($_POST['gender'] ?? ''),
        'skills' => $skills,
        'username' => trim($_POST['username'] ?? ''),
        'password' => trim($_POST['password'] ?? ''),
        'department' => trim($_POST['department'] ?? '')
    ];

    if ($isEditMode) { // Handle Edit Form
        try {
            // Check username uniqueness for update
            if (usernameExists($data['username'], $editId)) {
                header('Location: index.php?id=' . $editId . '&error=' . urlencode('Username already exists.'));
                exit;
            }
            
            updateRecord($editId, $data);
            header('Location: table.php?success=' . urlencode('User updated successfully.'));
            exit;
        } catch (Exception $e) {
            header('Location: index.php?id=' . $editId . '&error=' . urlencode('Error updating user: ' . $e->getMessage()));
            exit;
        }
    } else { // Handle Create Form
        try {
            // Check username uniqueness for create
            if (usernameExists($data['username'])) {
                header('Location: index.php?error=' . urlencode('Username already exists.'));
                exit;
            }
            
            $newId = createRecord($data);
            header('Location: table.php?success=' . urlencode('User created successfully with ID: ' . $newId));
            exit;
        } catch (Exception $e) {
            header('Location: index.php?error=' . urlencode('Error creating user: ' . $e->getMessage()));
            exit;
        }
    } 
}

// ─── Handle DELETE ───────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    try {
        if (deleteRecord($deleteId)) {
            header('Location: table.php?success=' . urlencode('User deleted successfully.'));
        } else {
            header('Location: table.php?error=' . urlencode('User not found or could not be deleted.'));
        }
    } catch (Exception $e) {
        header('Location: table.php?error=' . urlencode('Error deleting user: ' . $e->getMessage()));
    }
    exit;
}