<?php
session_start();

define('DATA_FILE', __DIR__ . '/data/users.csv');

include_once 'utils.php';

$records = readAllRecords();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .page-header {
            background: linear-gradient(135deg, #198754, #0dcaf0);
            color: white;
            padding: 20px 24px;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .table-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .table thead th {
            background-color: #343a40;
            color: #fff;
            font-weight: 500;
            white-space: nowrap;
        }

        .badge-skill {
            font-size: 0.72rem;
        }

        .action-btns .btn {
            min-width: 70px;
        }
    </style>
</head>

<body>
    <div class="container py-5">

        <!-- Header -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">All Registered Users</h1>
                <p class="mb-0 opacity-75">
                    Number of Records are
                    <?= count($records) ?>
                </p>
            </div>
            <a href="index.php" class="btn btn-light btn-sm">Add New User</a>
        </div>

        <!-- Table -->
        <div class="table-card">
            <?php if (empty($records)): ?>
                <div class="text-center py-5 text-muted">
                    <p class="fs-4">No records found.</p>
                    <a href="index.php" class="btn btn-primary">Add First User</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Country</th>
                                <th>Gender</th>
                                <th>Skills</th>
                                <th>Username</th>
                                <th>Department</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $index => $record): ?>
                                <tr>
                                    <td class="text-muted small">
                                        <?= $index + 1 ?>
                                    </td>
                                    <td>
                                        <?= $record['firstname'] ?>
                                    </td>
                                    <td>
                                        <?= $record['lastname'] ?>
                                    </td>
                                    <td>
                                        <?= $record['country'] ?>
                                    </td>
                                    <td>
                                        <?php $gender = $record['gender'];
                                        $badge = $gender === 'Male' ? 'primary' : 'danger'; ?>
                                        <span class="badge bg-<?= $badge ?>">
                                            <?= $gender ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $skills = explode('|', $record['skills']);
                                        foreach ($skills as $skill) {
                                            if (trim($skill) !== '') {
                                                echo "<span class='badge bg-secondary badge-skill me-1'>" . $skill . "</span>";
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td><?= $record['username'] ?></td>
                                    <td>
                                        <?= $record['department'] ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center action-btns">
                                            <!-- View -->
                                            <a href="view.php?id=<?= urlencode($record['id']) ?>"
                                                class="btn btn-sm btn-outline-info" title="View">
                                                View
                                            </a>
                                            <!-- Edit -->
                                            <a href="index.php?id=<?= urlencode($record['id']) ?>"
                                                class="btn btn-sm btn-outline-warning" title="Edit">
                                                Edit
                                            </a>
                                            <!-- Delete -->
                                            <a href="server.php?delete=<?= urlencode($record['id']) ?>"
                                                class="btn btn-sm btn-outline-danger" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this record?')">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>