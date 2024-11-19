<?php
require_once __DIR__ . '/../../App/Controller/UserController.php';
$first_name = isset($_SESSION['entered-first-name']) ? $_SESSION['entered-first-name'] : '';
$last_name = isset($_SESSION['entered-last-name']) ? $_SESSION['entered-first-name'] : '';
$user_name = isset($_SESSION['entered-username']) ? $_SESSION['entered-first-name'] : '';
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
</head>

<body>
<form action="../../App/Controller/UserController.php" method="post">
    <label for="first-name">First Name:</label>
    <input type="text"
           id="first-name"
           name="firstName"
           placeholder="Enter your first name"
           value="<?= $first_name; ?>"
           required/>

    <label for="last-name">Last Name:</label>
    <input type="text" id="last-name" name="lastName" placeholder="Enter your last name" value="<?= $last_name ?>"
           required/>

    <label for="username">Username:</label>
    <input type="text" id="username" name="userName" placeholder="Choose a username" value="<?= $user_name ?>"
           required/>
    <div class="error">
        <?php
        if (isset($_SESSION['error'])) {
            echo "<span>" . $_SESSION['error'] . "</span>";
            unset($_SESSION['error']);
        }
        ?>
    </div>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" placeholder="Enter a password" required/>

    <button type="submit" name="register" value="register">Register</button>
    <p>Already have an account? <a href="login.jsp">Login</a></p>
</form>
</body>

</html>