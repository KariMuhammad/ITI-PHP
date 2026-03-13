<?php
namespace App\Http\Controllers;

use App\Repositories\UserRepositoryInterface;
use Exception;

class UserController {
    protected $repository;

    public function __construct(UserRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function handleRequest() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->storeOrUpdate();
        } elseif (isset($_GET['delete'])) {
            $this->delete($_GET['delete']);
        }
    }

    protected function storeOrUpdate() {
        $editId = trim($_POST['edit_id'] ?? '');
        $isEditMode = !empty($editId);
        $errors = [];

        $maxImageSize = 2 * 1024 * 1024;
        $allowedImageTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        $profileImagePath = '';

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

        if (!$isEditMode) {
            $captchaInput = trim($_POST['captcha'] ?? '');
            if ($captchaInput !== ($_SESSION['captcha'] ?? '')) {
                $errors['captcha'] = 'Wrong captcha. Please try again.';
            }
        }

        if (!$this->isRequired($data['firstname'])) {
            $errors['firstname'] = 'First name is required.';
        } elseif (!$this->noNumbers($data['firstname'])) {
            $errors['firstname'] = 'First name must not contain numbers.';
        }

        if (!$this->isRequired($data['lastname'])) {
            $errors['lastname'] = 'Last name is required.';
        } elseif (!$this->noNumbers($data['lastname'])) {
            $errors['lastname'] = 'Last name must not contain numbers.';
        }

        if (!$this->isRequired($data['country'])) {
            $errors['country'] = 'Country is required.';
        } elseif (!$this->noNumbers($data['country'])) {
            $errors['country'] = 'Country must not contain numbers.';
        }

        if (!$this->isRequired($data['address'])) {
            $errors['address'] = 'Address is required.';
        }

        if (!$this->isRequired($data['gender'])) {
            $errors['gender'] = 'Gender is required.';
        }

        if (!$this->hasAtLeastOneSkill($skillsSelected)) {
            $errors['skills'] = 'Please select at least one skill.';
        }

        if (!$this->isRequired($data['username'])) {
            $errors['username'] = 'Username is required.';
        } elseif (!$this->isValidUsername($data['username'])) {
            $errors['username'] = 'Username must start with a letter and can include numbers.';
        }

        if (!$isEditMode || ($isEditMode && $data['password'] !== '')) {
            if (!$this->isRequired($data['password'])) {
                $errors['password'] = 'Password is required.';
            } elseif (!$this->checkPassword($data['password'])) {
                $errors['password'] = 'Password must be exactly 8 characters, only lowercase letters, numbers and underscore, no capital letters.';
            }
        }

        if (!$this->isRequired($data['department'])) {
            $errors['department'] = 'Department is required.';
        }

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
                    $uploadDir = __DIR__ . '/../../../uploads';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $filename = uniqid('profile_', true) . '.' . $ext;
                    $destination = $uploadDir . '/' . $filename;
                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        $profileImagePath = 'uploads/' . $filename;
                    } else {
                        $errors['profile_image'] = 'Could not save uploaded image.';
                    }
                }
            }
        }

        if ($isEditMode && $profileImagePath === '') {
            $existing = $this->repository->findById($editId);
            if ($existing && !empty($existing['profile_image'])) {
                $profileImagePath = $existing['profile_image'];
            }
        }

        $data['profile_image'] = $profileImagePath;

        if (empty($errors['username']) && $this->isRequired($data['username'])) {
            if ($this->repository->usernameExists($data['username'], $isEditMode ? $editId : null)) {
                $errors['username'] = 'Username already exists.';
            }
        }

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

        $data['skills'] = implode('|', $skillsSelected);

        try {
            if ($isEditMode) {
                if ($data['password'] === '') {
                    $existing = $this->repository->findById($editId);
                    if ($existing) {
                        $data['password'] = $existing['password'];
                    }
                }
                $this->repository->update($editId, $data);
                header('Location: table.php?success=' . urlencode('User updated successfully.'));
            } else {
                $newId = $this->repository->create($data);
                header('Location: table.php?success=' . urlencode('User created successfully with ID: ' . $newId));
            }
            exit;
        } catch (Exception $e) {
            $_SESSION['form_errors'] = ['general' => 'Error saving user: ' . $e->getMessage()];
            $_SESSION['old_data'] = $data;
            header('Location: ' . ($isEditMode ? 'index.php?id=' . urlencode($editId) : 'index.php'));
            exit;
        }
    }

    protected function delete($id) {
        try {
            if ($this->repository->delete($id)) {
                header('Location: table.php?success=' . urlencode('User deleted successfully.'));
            } else {
                header('Location: table.php?error=' . urlencode('User not found or could not be deleted.'));
            }
        } catch (Exception $e) {
            header('Location: table.php?error=' . urlencode('Error deleting user: ' . $e->getMessage()));
        }
        exit;
    }

    // Validation Methods (Moved from validation-utils.php)
    protected function isRequired($field) {
        return trim((string) $field) !== '';
    }

    protected function noNumbers($field) {
        return filter_var($field, FILTER_VALIDATE_REGEXP, [
            'options' => ['regexp' => '/^[a-zA-Z\s]+$/']
        ]) !== false;
    }

    protected function isValidUsername($field) {
        if (!$this->isRequired($field)) return false;
        return preg_match('/^[A-Za-z][A-Za-z0-9]*$/', $field) === 1;
    }

    protected function checkPassword($field) {
        $field = (string) $field;
        if (strlen($field) !== 8) return false;
        if (preg_match('/[A-Z]/', $field)) return false;
        if (!preg_match('/^[a-z0-9_]+$/', $field)) return false;
        return true;
    }

    protected function hasAtLeastOneSkill($skills) {
        if (!is_array($skills)) return false;
        $filtered = array_filter($skills, function ($value) {
            return trim((string) $value) !== '';
        });
        return count($filtered) > 0;
    }

    public function getAllUsers() {
        return $this->repository->findAll();
    }

    public function getUserById($id) {
        return $this->repository->findById($id);
    }

}
