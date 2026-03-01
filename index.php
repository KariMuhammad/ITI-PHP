<?php
// firstname, lastname, address, country, gender, skills, username,password, department
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day 1</title>

    <!-- import bootstrap css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <header>
        <h1 class="text-center">Day 1</h1>
        <p class="text-center">Form Handling</p>
    </header>
    <div class="container">
        <form action="server.php" method="get">
            <div class="row">
                <div class="mb-3 col">
                    <label for="firstname">First Name</label>
                    <input class="form-control" type="text" name="firstname" id="firstname" pattern="[A-Za-z]+" title="Only letters are allowed">
                </div>
                <div class="mb-3 col">
                    <label for="lastname">Last Name</label>
                    <input class="form-control" type="text" name="lastname" id="lastname" pattern="[A-Za-z]+" title="Only letters are allowed">
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col">
                    <label for="country">Country</label>
                    <input class="form-control" type="text" name="country" id="country" pattern="[A-Za-z]+" title="Only letters are allowed">
                </div>
                <div class="mb-3 col">
                    <label for="address">Address</label>
                    <input class="form-control" type="text" name="address" id="address" pattern="A-Za-z]+" title="Only letters are aloowed">
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col">
                    <label for="gender">Gender</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="gender" value="male">
                        <label class="form-check-label" for="gender">
                            Male
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="gender" value="female">
                        <label class="form-check-label" for="gender">
                            Female
                        </label>
                    </div>
                </div>
                <div class="mb-3 col d-flex align-items-center gap-2">
                    <label for="skills" class="badge bg-primary">Skills</label>

                    <label for="skills">PHP</label>
                    <input type="checkbox" name="skills[]" id="skills" value="php">
                    <label for="skills">HTML</label>
                    <input type="checkbox" name="skills[]" id="skills" value="html">
                    <label for="skills">CSS</label>
                    <input type="checkbox" name="skills[]" id="skills" value="css">
                </div>
            </div>

            <fieldset>
                <legend class="text-xl fw-bold text-secondary">Account Information</legend>
                <div class="row">
                    <div class="mb-3 col">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" pattern="[A-Za-z]+[0-9]*" title="Only letters with number">
                    </div>
                    <div class="mb-3 col">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password">
                    </div>
                    <div class="mb-3 col">
                        <label for="department">Department</label>
                        <input type="text" name="department" id="department">
                    </div>
                </div>
            </fieldset>

            <input class="btn btn-success w-100" type="submit" value="Submit">
        </form>
    </div>
</body>

</html>

<?php
