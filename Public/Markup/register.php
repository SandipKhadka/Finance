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
           value="<?php echo isset($_POST['firstName'])? $_POST['firstName']: ''; ?>"
           required/>

    <label for="last-name">Last Name:</label>
    <input type="text" id="last-name" name="lastName" placeholder="Enter your last name" required/>

    <label for="username">Username:</label>
    <input type="text" id="username" name="userName" placeholder="Choose a username" required/>
    <div class="error">
        <?php
        if (isset($_GET['error'])) {
            echo "<span>" . $_GET['error'] . "</span>";
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