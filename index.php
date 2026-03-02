<?php
session_start();

// ─── Determine if we're in Edit Mode

define('DATA_FILE', __DIR__ . '/data/users.csv');

$isEditMode = false;
$record = null;
$currentSkills = [];

$editId = $_GET['id'] ?? null;

if ($editId) {
    // Load the record to edit
    if (file_exists(DATA_FILE)) {
        $file = fopen(DATA_FILE, 'r');
        $headers = fgetcsv($file);
        
        while (!feof($file)) {
            $row = fgetcsv($file);

            if ($row) {
                $r = array_combine($headers, $row);
                if ($r['id'] === $editId) {
                    $record = $r;
                    $isEditMode = true;
                    $currentSkills = array_filter(explode('|', $record['skills']));
                    break;
                }
            }
        }

        fclose($file);
    }
    
    if (!$record) {
        header('Location: table.php');
        exit;
    }
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
                            Editing: <strong><?= ($record['firstname'] . ' ' . $record['lastname']) ?></strong>
                        </p>
                    <?php else: ?>
                        <h1 class="h3 mb-1">User Registration</h1>
                        <p class="mb-0 opacity-75">Fill in the details below to register a new user</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Success / Error Messages -->
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
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
                <form action="server.php" method="post">

                    <!-- Hidden field for edit mode -->
                    <?php if ($isEditMode): ?>
                        <input type="hidden" name="edit_id" value="<?= ($editId) ?>">
                    <?php endif; ?>

                    <!-- Personal Info -->
                    <h5 class="text-muted mb-3 border-bottom pb-2">Personal Information</h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="firstname" class="form-label">First Name</label>
                            <input class="form-control" type="text" name="firstname" id="firstname" 
                                value="<?= $isEditMode ? ($record['firstname']) : '' ?>"
                                pattern="[A-Za-z]+" title="Only letters are allowed" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input class="form-control" type="text" name="lastname" id="lastname" 
                                value="<?= $isEditMode ? ($record['lastname']) : '' ?>"
                                pattern="[A-Za-z]+" title="Only letters are allowed" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="country" class="form-label">Country</label>
                            <input class="form-control" type="text" name="country" id="country" 
                                value="<?= $isEditMode ? ($record['country']) : '' ?>"
                                pattern="[A-Za-z ]+" title="Only letters are allowed" required>
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input class="form-control" type="text" name="address" id="address" 
                                value="<?= $isEditMode ? ($record['address']) : '' ?>"
                                required>
                        </div>
                    </div>

                    <!-- Gender & Skills -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="male" value="Male"
                                        <?= ($isEditMode && $record['gender'] === 'Male') ? 'checked' : (!$isEditMode ? 'required' : '') ?>
                                        <?= !$isEditMode ? 'required' : '' ?>>
                                    <label class="form-check-label" for="male">Male</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="female"
                                        value="Female"
                                        <?= ($isEditMode && $record['gender'] === 'Female') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="female">Female</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Skills</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="skills[]" id="skill_php"
                                        value="PHP" <?= ($isEditMode && in_array('PHP', $currentSkills)) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="skill_php">PHP</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="skills[]" id="skill_html"
                                        value="HTML" <?= ($isEditMode && in_array('HTML', $currentSkills)) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="skill_html">HTML</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="skills[]" id="skill_css"
                                        value="CSS" <?= ($isEditMode && in_array('CSS', $currentSkills)) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="skill_css">CSS</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="skills[]" id="skill_js"
                                        value="JavaScript" <?= ($isEditMode && in_array('JavaScript', $currentSkills)) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="skill_js">JavaScript</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Info -->
                    <h5 class="text-muted mb-3 border-bottom pb-2 mt-4">Account Information</h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="username" class="form-label">Username</label>
                            <input class="form-control" type="text" name="username" id="username"
                                value="<?= $isEditMode ? ($record['username']) : '' ?>"
                                pattern="[A-Za-z][A-Za-z0-9]*" title="Start with a letter, can include numbers"
                                required>
                        </div>
                        <div class="col-md-4">
                            <label for="password" class="form-label">Password</label>
                            <input class="form-control" type="password" name="password" id="password" 
                                placeholder="Password"
                                <?= !$isEditMode ? 'required' : '' ?>>
                        </div>
                        <div class="col-md-4">
                            <label for="department" class="form-label">Department</label>
                            <input class="form-control" type="text" name="department" id="department" 
                                value="<?= $isEditMode ? ($record['department']) : '' ?>"
                                required>
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
</body>

</html>