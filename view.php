<?php
session_start();
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/app/Repositories/UserRepository.php';
require_once __DIR__ . '/app/Decorators/LoggingUserRepository.php';
require_once __DIR__ . '/app/Http/Controllers/UserController.php';

use App\Repositories\UserRepository;
use App\Decorators\LoggingUserRepository;
use App\Http\Controllers\UserController;

$baseRepository = new UserRepository();
$repository = new LoggingUserRepository($baseRepository);
$controller = new UserController($repository);

// Get the requested ID
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: table.php');
    exit;
}

// Find the record from database
$record = $controller->getUserById($id);
// die(json_encode($record));
if (!$record) {
    header('Location: table.php?error=' . urlencode('User not found.'));
    exit;
}

$skills = array_filter(explode('|', $record['skills']));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User –
        <?= $record['firstname'] . ' ' . $record['lastname'] ?>
    </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .page-header {
            background: linear-gradient(135deg, #0dcaf0, #6610f2);
            color: white;
            padding: 20px 24px;
            border-radius: 12px 12px 0 0;
        }

        .label-col {
            width: 160px;
            font-weight: 600;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <!-- navbar -->
    <div class="container py-5" style="max-width: 700px;">
        <div class="card">
            <!-- Header -->
            <div class="page-header d-flex align-items-center justify-content-between gap-3">
                <div>
        <div class="d-flex gap-3" id="navbarNav">
            <a href="./table.php" class="btn btn-sm btn-light">Table</a>
            <a href="./index.php" class="btn btn-sm btn-light">Create</a>
        </div>
    </nav>
                        <?= $record['firstname'] ?>
                        <?= $record['lastname'] ?>
                    </h1>
                    <p class="mb-0 opacity-75">
                        <?= $record['department'] ?> Department
                    </p>
                </div>
                <div>
                    <?php if (isset($_SESSION['username'])): ?>
                        <span class="small">Logged in as
                            <strong><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES) ?></strong></span>
                        <a href="auth/logout.php" class="btn btn-sm btn-light">Logout</a>
                </div>
                <?php else: ?>
                    <a href="auth/login.php" class="btn btn-sm btn-light">Login</a>
                <?php endif; ?>
            </div>

            <!-- Details -->
            <div class="card-body">
                <h5 class="text-muted border-bottom pb-2 mb-3">Personal Information</h5>
                <table class="">
                    <tr>
                        <td class="label-col">First Name</td>
                        <td>
                            <?= $record['firstname'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">Last Name</td>
                        <td>
                            <?= $record['lastname'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">Country</td>
                        <td>
                            <?= $record['country'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">Address</td>
                        <td>
                            <?= $record['address'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">Gender</td>
                        <td>
                            <?php $badge = $record['gender'] === 'Male' ? 'primary' : 'danger'; ?>
                            <span class="badge bg-<?= $badge ?>">
                                <?= ($record['gender']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">Skills</td>
                        <td>
                            <?php foreach ($skills as $skill): ?>
                                <span class="badge bg-secondary me-1">
                                    <?= ($skill) ?>
                                </span>
                            <?php endforeach; ?>
                            <?php if (empty($skills)): ?>
                                <span class="text-muted">None specified</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <h5 class="text-muted border-bottom pb-2 mb-3 mt-4">Account Information</h5>
                <table class="">
                    <tr>
                        <td class="label-col">Username</td>
                        <td><?= $record['username'] ?></td>
                    </tr>
                    <tr>
                        <td class="label-col">Department</td>
                        <td>
                            <?= ($record['department']) ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>