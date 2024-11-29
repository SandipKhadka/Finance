<?php
require_once __DIR__ . '/../../App/Controller/UserController.php';
$first_name = isset($_SESSION['entered-first-name']) ? $_SESSION['entered-first-name'] : '';
$last_name = isset($_SESSION['entered-last-name']) ? $_SESSION['entered-last-name'] : '';
$user_name = isset($_SESSION['entered-username']) ? $_SESSION['entered-username'] : '';
unset($_SESSION['entered-first-name']);
unset($_SESSION['entered-last-name']);
unset($_SESSION['entered-username']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Registration Form</title>
    <link rel="stylesheet" href="../CSS/register.css"/>
    <script>
        function validateForm(event) {


            const firstName = document.getElementById('first-name');
            const lastName = document.getElementById('last-name');
            const username = document.getElementById('username');
            const password = document.getElementById('password');

            let valid = true;

            document.querySelectorAll('.error').forEach(error => error.textContent = '');

            if (!/^[a-zA-Z]+$/.test(firstName.value)) {
                showError(firstName, 'First name must only contain letters.');
                valid = false;
            }

            if (!/^[a-zA-Z]+$/.test(lastName.value)) {
                showError(lastName, 'Last name must only contain letters.');
                valid = false;
            }

            if (username.value.length < 3) {
                showError(username, 'Username must be at least 3 characters long.');
                valid = false;
            }

            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
            if (!passwordRegex.test(password.value)) {
                showError(password, 'Password must be at least 8 characters long, include one uppercase letter, one lowercase letter, and one number.');
                valid = false;
            }

            if(!valid) {
                event.preventDefault();
            }
        }

        function showError(input, message) {
            const errorDiv = input.nextElementSibling || document.createElement('div');
            errorDiv.className = 'error';
            errorDiv.textContent = message;
            input.parentNode.insertBefore(errorDiv, input.nextSibling);
        }
    </script>
</head>

<body>
<form action="../../App/Controller/UserController.php" method="post" onsubmit="validateForm(event)">
    <label for="first-name">First Name:</label>
    <input type="text" id="first-name" name="firstName" placeholder="Enter your first name" value="<?= $first_name; ?>"
           required/>
    <div class="error"></div>

    <label for="last-name">Last Name:</label>
    <input type="text" id="last-name" name="lastName" placeholder="Enter your last name" value="<?= $last_name ?>"
           required/>
    <div class="error"></div>

    <label for="username">Username:</label>
    <input type="text" id="username" name="userName" placeholder="Choose a username" value="<?= $user_name ?>"
           required/>
    <div class="error">
        <?php
        if (isset($_SESSION['username-error'])) {
            echo "<span>" . $_SESSION['username-error'] . "</span>";
            unset($_SESSION['username-error']);
        }
        ?>
    </div>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" placeholder="Enter a password" required/>
    <div class="error"></div>

    <button type="submit" name="register" value="register">Register</button>
    <p>Already have an account? <a href="../../index.php">Login</a></p>
</form>
</body>

</html>