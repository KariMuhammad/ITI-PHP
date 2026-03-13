<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

use App\Repositories\UserRepository;
use App\Decorators\LoggingUserRepository;
use App\Http\Controllers\UserController;

$baseRepository = new UserRepository();
$repository = new LoggingUserRepository($baseRepository);
$controller = new UserController($repository);

$is_admin = $_SESSION['username'] === 'Admin';
if (!$is_admin) {
    header('Location: auth/login.php');
    exit;
}

$isEditMode = false;
$record = null;
$currentSkills = [];

$editId = $_GET['id'] ?? null;

if ($editId) {
    $record = $controller->getUserById($editId);
    if ($record) {
        $isEditMode = true;
        $currentSkills = array_filter(explode('|', $record['skills']));
    } else {
        header('Location: table.php?error=' . urlencode('User not found.'));
        exit;
    }
}

// Validation errors and old values from previous submit
$errors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['old_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['old_data']);

if (!empty($old) && isset($old['skills']) && is_array($old['skills'])) {
    $currentSkills = $old['skills'];
}

// ─── Generate Captcha (only for new registrations) ----

if (!$isEditMode) {
    $strings = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $captcha = substr(str_shuffle($strings), 0, 5);
    $_SESSION['captcha'] = $captcha;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .page-header {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: white;
            padding: 20px;
            border-radius: 12px 12px 0 0;
        }
    </style>
</head>

<body>
    <div class="container py-5">

        <!-- Page Header -->
        <div class="form-card mb-0">
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <?php if ($isEditMode): ?>
                        <h1 class="h3 mb-1">Edit User</h1>
                        <p class="mb-0 opacity-75">
                            Editing:
                            <strong><?= htmlspecialchars($record['firstname'] . ' ' . $record['lastname'], ENT_QUOTES) ?></strong>
                        </p>
                    <?php else: ?>
                        <h1 class="h3 mb-1">User Registration</h1>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if (isset($_SESSION['username'])): ?>
                        <span class="small">Logged in as
                            <strong><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES) ?></strong></span>
                    <?php else: ?>
                        <a href="auth/login.php" class="btn btn-sm btn-light">Login</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Success / Error Messages -->
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger m-3 mb-0" role="alert">
                    <?= htmlspecialchars($errors['general'], ENT_QUOTES) ?>
                </div>
            <?php elseif (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success alert-dismissible fade show m-3 mb-0" role="alert">
                    User saved successfully! <a href="table.php" class="alert-link">go to table</a>
                </div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-danger m-3 mb-0" role="alert">
                    <?= ($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="card-body p-4">
                <form action="server.php" method="post" id="userForm" enctype="multipart/form-data">

                    <!-- Hidden field for edit mode -->
                    <?php if ($isEditMode): ?>
                        <?= $editId ?>
                        <input type="hidden" name="edit_id" value="<?= ($editId) ?>">
                    <?php endif; ?>

                    <!-- Personal Info -->
                    <h5 class="text-muted mb-3 border-bottom pb-2">Personal Information</h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="firstname" class="form-label">First Name</label>
                            <input class="form-control" type="text" name="firstname" id="firstname"
                                value="<?= htmlspecialchars(isset($old['firstname']) ? $old['firstname'] : ($isEditMode && $record ? $record['firstname'] : ''), ENT_QUOTES) ?>"
                                pattern="[A-Za-z\s]+" title="Only letters are allowed" required>
                            <div id="firstname_error" class="text-danger small mt-1">
                                <?= !empty($errors['firstname']) ? htmlspecialchars($errors['firstname'], ENT_QUOTES) : '' ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input class="form-control" type="text" name="lastname" id="lastname"
                                value="<?= htmlspecialchars(isset($old['lastname']) ? $old['lastname'] : ($isEditMode && $record ? $record['lastname'] : ''), ENT_QUOTES) ?>"
                                pattern="[A-Za-z\s]+" title="Only letters are allowed" required>
                            <div id="lastname_error" class="text-danger small mt-1">
                                <?= !empty($errors['lastname']) ? htmlspecialchars($errors['lastname'], ENT_QUOTES) : '' ?>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="country" class="form-label">Country</label>
                            <input class="form-control" type="text" name="country" id="country"
                                value="<?= htmlspecialchars(isset($old['country']) ? $old['country'] : ($isEditMode && $record ? $record['country'] : ''), ENT_QUOTES) ?>"
                                pattern="[A-Za-z ]+" title="Only letters are allowed" required>
                            <div id="country_error" class="text-danger small mt-1">
                                <?= !empty($errors['country']) ? htmlspecialchars($errors['country'], ENT_QUOTES) : '' ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input class="form-control" type="text" name="address" id="address"
                                value="<?= htmlspecialchars(isset($old['address']) ? $old['address'] : ($isEditMode && $record ? $record['address'] : ''), ENT_QUOTES) ?>"
                                required>
                            <div id="address_error" class="text-danger small mt-1">
                                <?= !empty($errors['address']) ? htmlspecialchars($errors['address'], ENT_QUOTES) : '' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Gender & Skills -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="male" value="Male"
                                        <?= ($isEditMode && $record && $record['gender'] === 'Male') || (!$isEditMode && ($old['gender'] ?? '') === 'Male') ? 'checked' : '' ?>
                                        <?= !$isEditMode ? 'required' : '' ?>>
                                    <label class="form-check-label" for="male">Male</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="female"
                                        value="Female" <?= ($isEditMode && $record && $record['gender'] === 'Female') || (!$isEditMode && ($old['gender'] ?? '') === 'Female') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="female">Female</label>
                                </div>
                            </div>
                            <div id="gender_error" class="text-danger small mt-1">
                                <?= !empty($errors['gender']) ? htmlspecialchars($errors['gender'], ENT_QUOTES) : '' ?>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Skills</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="skills[]" id="skill_php"
                                        value="PHP" <?= in_array('PHP', $currentSkills) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="skill_php">PHP</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="skills[]" id="skill_html"
                                        value="HTML" <?= in_array('HTML', $currentSkills) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="skill_html">HTML</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="skills[]" id="skill_css"
                                        value="CSS" <?= in_array('CSS', $currentSkills) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="skill_css">CSS</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="skills[]" id="skill_js"
                                        value="JavaScript" <?= in_array('JavaScript', $currentSkills) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="skill_js">JavaScript</label>
                                </div>
                            </div>
                            <div id="skills_error" class="text-danger small mt-1">
                                <?= !empty($errors['skills']) ? htmlspecialchars($errors['skills'], ENT_QUOTES) : '' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Account Info -->
                    <h5 class="text-muted mb-3 border-bottom pb-2 mt-4">Account Information</h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="username" class="form-label">Username</label>
                            <input class="form-control" type="text" name="username" id="username"
                                value="<?= htmlspecialchars(isset($old['username']) ? $old['username'] : ($isEditMode && $record ? $record['username'] : ''), ENT_QUOTES) ?>"
                                pattern="[A-Za-z][A-Za-z0-9]*" title="Start with a letter, can include numbers"
                                required>
                            <div id="username_error" class="text-danger small mt-1">
                                <?= !empty($errors['username']) ? htmlspecialchars($errors['username'], ENT_QUOTES) : '' ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="password" class="form-label">Password</label>
                            <input class="form-control" type="password" name="password" id="password"
                                placeholder="Password"
                                pattern="^[a-z0-9_]{8}$"
                                minlength="8" maxlength="16"
                                title="Exactly 8 characters, only lowercase letters, numbers and underscore"
                                <?= !$isEditMode ? 'required' : '' ?>>
                            <div id="password_error" class="text-danger small mt-1">
                                <?= !empty($errors['password']) ? htmlspecialchars($errors['password'], ENT_QUOTES) : '' ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="department" class="form-label">Department</label>
                            <input class="form-control" type="text" name="department" id="department"
                                value="<?= htmlspecialchars(isset($old['department']) ? $old['department'] : ($isEditMode && $record ? $record['department'] : ''), ENT_QUOTES) ?>"
                                required>
                            <div id="department_error" class="text-danger small mt-1">
                                <?= !empty($errors['department']) ? htmlspecialchars($errors['department'], ENT_QUOTES) : '' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Picture (optional) -->
                    <div class="mb-4">
                        <label for="profile_image" class="form-label">Profile Picture (JPG or PNG, max 2MB)</label>
                        <input class="form-control" type="file" name="profile_image" id="profile_image"
                            accept="image/jpeg,image/png">
                        <div class="form-text">This field is optional.</div>
                        <div id="profile_image_error" class="text-danger small mt-1">
                            <?= !empty($errors['profile_image']) ? htmlspecialchars($errors['profile_image'], ENT_QUOTES) : '' ?>
                        </div>
                    </div>

                    <!-- Captcha (only for new registrations) -->
                    <?php if (!$isEditMode): ?>
                    <div class="mb-4">
                        <label class="form-label">Captcha</label>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-secondary fs-5 px-3 py-2 font-monospace"><?= $captcha ?></span>
                            <input class="form-control" type="text" name="captcha" id="captcha"
                                placeholder="Enter captcha above" required>
                            <div id="captcha_error" class="text-danger small mt-1">
                                <?= !empty($errors['captcha']) ? htmlspecialchars($errors['captcha'], ENT_QUOTES) : '' ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <button class="btn <?= $isEditMode ? 'btn-primary' : 'btn-secondary' ?> w-100 btn-lg" type="submit">
                        <?= $isEditMode ? 'Save Changes' : 'Save User' ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('userForm');
            if (!form) return;

            form.addEventListener('submit', function (e) {
                var valid = true;

                function setError(id, message) {
                    var el = document.getElementById(id);
                    if (el) {
                        el.textContent = message || '';
                    }
                }

                // clear previous errors
                setError('firstname_error', '');
                setError('lastname_error', '');
                setError('country_error', '');
                setError('address_error', '');
                setError('gender_error', '');
                setError('skills_error', '');
                setError('username_error', '');
                setError('password_error', '');
                setError('department_error', '');
                setError('captcha_error', '');
                setError('profile_image_error', '');

                var firstname = document.getElementById('firstname').value.trim();
                var lastname = document.getElementById('lastname').value.trim();
                var country = document.getElementById('country').value.trim();
                var address = document.getElementById('address').value.trim();
                var username = document.getElementById('username').value.trim();
                var password = document.getElementById('password').value.trim();
                var department = document.getElementById('department').value.trim();
                var captchaInput = document.getElementById('captcha');
                var profileInput = document.getElementById('profile_image');

                var male = document.getElementById('male');
                var female = document.getElementById('female');

                var skillPhp = document.getElementById('skill_php');
                var skillHtml = document.getElementById('skill_html');
                var skillCss = document.getElementById('skill_css');
                var skillJs = document.getElementById('skill_js');

                var nameRegex = /^[A-Za-z\s]+$/;
                var passwordRegex = /^[a-z0-9_]{8}$/;
                var maxImageSize = 2 * 1024 * 1024; // 2MB

                if (firstname === '') {
                    setError('firstname_error', 'First name is required.');
                    valid = false;
                } else if (!nameRegex.test(firstname)) {
                    setError('firstname_error', 'First name must not contain numbers.');
                    valid = false;
                }

                if (lastname === '') {
                    setError('lastname_error', 'Last name is required.');
                    valid = false;
                } else if (!nameRegex.test(lastname)) {
                    setError('lastname_error', 'Last name must not contain numbers.');
                    valid = false;
                }

                if (country === '') {
                    setError('country_error', 'Country is required.');
                    valid = false;
                } else if (!nameRegex.test(country)) {
                    setError('country_error', 'Country must not contain numbers.');
                    valid = false;
                }

                if (address === '') {
                    setError('address_error', 'Address is required.');
                    valid = false;
                }

                if (!male.checked && !female.checked) {
                    setError('gender_error', 'Gender is required.');
                    valid = false;
                }

                var skillsChecked = (skillPhp && skillPhp.checked) ||
                    (skillHtml && skillHtml.checked) ||
                    (skillCss && skillCss.checked) ||
                    (skillJs && skillJs.checked);

                if (!skillsChecked) {
                    setError('skills_error', 'Please select at least one skill.');
                    valid = false;
                }

                if (username === '') {
                    setError('username_error', 'Username is required.');
                    valid = false;
                } else if (!/^[A-Za-z][A-Za-z0-9]*$/.test(username)) {
                    setError('username_error', 'Username must start with a letter and can include numbers.');
                    valid = false;
                }

                if (password === '' && !<?= $isEditMode ? 'true' : 'false' ?>) {
                    setError('password_error', 'Password is required.');
                    valid = false;
                } else if (password !== '' && !passwordRegex.test(password)) {
                    setError(
                        'password_error',
                        'Password must be exactly 8 characters, only lowercase letters, numbers and underscore, no capital letters.'
                    );
                    valid = false;
                }

                if (department === '') {
                    setError('department_error', 'Department is required.');
                    valid = false;
                }

                // Profile image client-side checks (optional)
                if (profileInput && profileInput.files && profileInput.files.length > 0) {
                    var file = profileInput.files[0];
                    var allowedTypes = ['image/jpeg', 'image/png'];

                    if (allowedTypes.indexOf(file.type) === -1) {
                        setError('profile_image_error', 'Only JPG and PNG images are allowed.');
                        valid = false;
                    } else if (file.size > maxImageSize) {
                        setError('profile_image_error', 'Image is too large. Max 2MB.');
                        valid = false;
                    }
                }

                // Captcha client-side: only check presence; value is fully validated on server
                if (captchaInput && captchaInput.value.trim() === '') {
                    setError('captcha_error', 'Captcha is required.');
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>

</html>