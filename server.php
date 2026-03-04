<?php
session_start();
include_once 'utils.php';
include_once 'validation-utils.php';

// ─── Handle Form Submission (POST) ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = trim($_POST['edit_id'] ?? '');
    $isEditMode = !empty($editId);
    $errors = [];

    // For image upload
    $maxImageSize = 2 * 1024 * 1024; // 2MB
    $allowedImageTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];
    $profileImagePath = '';

    // Prepare raw data (skills as array for validation)
    $skillsSelected = isset($_POST['skills']) ? (array) $_POST['skills'] : [];

    $data = [
        'firstname' => trim($_POST['firstname'] ?? ''),
        'lastname' => trim($_POST['lastname'] ?? ''),
        'country' => trim($_POST['country'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'gender' => trim($_POST['gender'] ?? ''),
        'skills' => $skillsSelected,
        'username' => trim($_POST['username'] ?? ''),
        'password' => trim($_POST['password'] ?? ''),
        'department' => trim($_POST['department'] ?? ''),
        'profile_image' => ''
    ];

    // Captcha (only for create)
    if (!$isEditMode) {
        $captchaInput = trim($_POST['captcha'] ?? '');
        if ($captchaInput !== ($_SESSION['captcha'] ?? '')) {
            $errors['captcha'] = 'Wrong captcha. Please try again.';
        }
    }

    // Server-side validation
    if (!is_required($data['firstname'])) {
        $errors['firstname'] = 'First name is required.';
    } elseif (!no_numbers($data['firstname'])) {
        $errors['firstname'] = 'First name must not contain numbers.';
    }

    if (!is_required($data['lastname'])) {
        $errors['lastname'] = 'Last name is required.';
    } elseif (!no_numbers($data['lastname'])) {
        $errors['lastname'] = 'Last name must not contain numbers.';
    }

    if (!is_required($data['country'])) {
        $errors['country'] = 'Country is required.';
    } elseif (!no_numbers($data['country'])) {
        $errors['country'] = 'Country must not contain numbers.';
    }

    if (!is_required($data['address'])) {
        $errors['address'] = 'Address is required.';
    }

    if (!is_required($data['gender'])) {
        $errors['gender'] = 'Gender is required.';
    }

    if (!has_at_least_one_skill($skillsSelected)) {
        $errors['skills'] = 'Please select at least one skill.';
    }

    if (!is_required($data['username'])) {
        $errors['username'] = 'Username is required.';
    } elseif (!is_valid_username($data['username'])) {
        $errors['username'] = 'Username must start with a letter and can include numbers.';
    }

    if (!$isEditMode || ($isEditMode && $data['password'] !== '')) {
        if (!is_required($data['password'])) {
            $errors['password'] = 'Password is required.';
        } elseif (!check_password($data['password'])) {
            $errors['password'] = 'Password must be exactly 8 characters, only lowercase letters, numbers and underscore, no capital letters.';
        }
    }

    if (!is_required($data['department'])) {
        $errors['department'] = 'Department is required.';
    }

    // Image upload validation (optional)
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['profile_image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors['profile_image'] = 'Error uploading image.';
        } elseif ($file['size'] > $maxImageSize) {
            $errors['profile_image'] = 'Image is too large. Max 2MB.';
        } else {
            $mime = mime_content_type($file['tmp_name']);
            if (!isset($allowedImageTypes[$mime])) {
                $errors['profile_image'] = 'Only JPG and PNG images are allowed.';
            } else {
                $ext = $allowedImageTypes[$mime];
                $uploadDir = __DIR__ . '/uploads';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filename = uniqid('profile_', true) . '.' . $ext;
                $destination = $uploadDir . '/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    // store relative path
                    $profileImagePath = 'uploads/' . $filename;
                } else {
                    $errors['profile_image'] = 'Could not save uploaded image.';
                }
            }
        }
    }

    // For edit mode, keep old image if no new upload
    if ($isEditMode && $profileImagePath === '') {
        $existing = readRecord($editId);
        if ($existing && !empty($existing['profile_image'])) {
            $profileImagePath = $existing['profile_image'];
        }
    }

    $data['profile_image'] = $profileImagePath;

    // Username uniqueness
    if (empty($errors['username']) && is_required($data['username'])) {
        if ($isEditMode) {
            if (usernameExists($data['username'], $editId)) {
                $errors['username'] = 'Username already exists.';
            }
        } else {
            if (usernameExists($data['username'])) {
                $errors['username'] = 'Username already exists.';
            }
        }
    }

    // If there are validation errors, redirect back with messages
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_data'] = $data;

        $redirectUrl = 'index.php';
        if ($isEditMode) {
            $redirectUrl .= '?id=' . urlencode($editId);
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    // Prepare skills string for database
    $data['skills'] = implode('|', $skillsSelected);

    if ($isEditMode) { // Handle Edit Form
        try {
            // Keep old password if none provided
            if ($data['password'] === '') {
                $existing = readRecord($editId);
                if ($existing) {
                    $data['password'] = $existing['password'];
                }
            }

            updateRecord($editId, $data);
            header('Location: table.php?success=' . urlencode('User updated successfully.'));
            exit;
        } catch (Exception $e) {
            $_SESSION['form_errors'] = ['general' => 'Error updating user: ' . $e->getMessage()];
            $_SESSION['old_data'] = $data;
            header('Location: index.php?id=' . urlencode($editId));
            exit;
        }
    } else { // Handle Create Form
        try {
            $newId = createRecord($data);
            header('Location: table.php?success=' . urlencode('User created successfully with ID: ' . $newId));
            exit;
        } catch (Exception $e) {
            $_SESSION['form_errors'] = ['general' => 'Error creating user: ' . $e->getMessage()];
            $_SESSION['old_data'] = $data;
            header('Location: index.php');
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