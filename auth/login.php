<?php
session_start();
$is_logged_in = isset($_SESSION['username']);
if ($is_logged_in) {
    return header('Location: ../view.php?id=' . urlencode($_SESSION['user_id']));
}

$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 480px;
            margin: 40px auto;
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
    <div class="container">
        <div class="form-card">
            <h1 class="h4 text-center">Login</h1>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger m-3 mb-0" role="alert">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="card-body p-4">
                <form action="LoginController.php" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input class="form-control" type="text" name="username" id="username"
                            pattern="[A-Za-z][A-Za-z0-9]*" title="Start with a letter, can include numbers" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input class="form-control" type="password" name="password" id="password" placeholder="Password"
                            required>
                    </div>

                    <button class="btn btn-primary w-100 btn-lg" type="submit">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>