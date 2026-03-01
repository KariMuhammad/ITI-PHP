<?php

// Show all data sent in index.php form
// firstname, lastname, address, country, gender, skills, username,password, department


$formData = $_GET;
unset($formData["skills"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Data</title>

    <!-- import bootstrap css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <header>
        <h1 class="text-center">Day 1</h1>
        <p class="text-center">Form Handling</p>
    </header>

    <div class="container">
        <h1>Personal Information Data</h1>
        <?php foreach ($formData as $key => $value) {
            echo "<div class='row'>";
            echo "<p class='col text-capitalize '>$key: </p>";
            echo "<p class='col'>$value</p>";
            echo "</div>";
        }
        ?>

        <h1>Skills</h1>
        <div class="gap-2">
            <?php
            if (isset($_GET["skills"])) {
                foreach ($_GET["skills"] as $skill) {
                    echo "<span class='col badge bg-primary text-white text-uppercase mx-1 px-4 py-2'>$skill</span>";
                }
            }
            ?>
        </div>
    </div>

</body>

</html>