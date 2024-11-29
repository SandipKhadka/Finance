<?php
session_start();
$previous_password = isset($_SESSION['entered-previous-password']) ? $_SESSION['entered-previous-password'] : '';
$new_password = isset($_SESSION['entered-new-password']) ? $_SESSION['entered-new-password'] : '';
$confirm_password = isset($_SESSION['entered-confirm-password']) ? $_SESSION['entered-confirm-password'] : '';
unset($_SESSION['entered-previous-password']);
unset($_SESSION['entered-new-password']);
unset($_SESSION['entered-confirm-password']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <title>Change Password</title>
    <script>
        function validateForm(event) {
            document.getElementById('previousPasswordError').innerHTML = '';
            document.getElementById('newPasswordError').innerHTML = '';
            document.getElementById('confirmPasswordError').innerHTML = '';
            var previousPassword = document.getElementById('previous-pass').value;
            var newPassword = document.getElementById('new-pass').value;
            var confirmPassword = document.getElementById('confirm-pass').value;
            var isValid = true;
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
            if (previousPassword === '') {
                document.getElementById('previousPasswordError').innerHTML = 'Please enter your old password.';
                isValid = false;
            }
            if (newPassword === '') {
                document.getElementById('newPasswordError').innerHTML = 'Please enter a new password.';
                isValid = false;
            } else if (!passwordRegex.test(newPassword)) {
                document.getElementById('newPasswordError').innerHTML = 'New password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, and one number.';
                isValid = false;
            }
            if (newPassword !== confirmPassword) {
                document.getElementById('confirmPasswordError').innerHTML = 'Passwords do not match.';
                isValid = false;
            }
            if (!isValid) {
                event.preventDefault();
            }
        }
    </script>
</head>
<body>
<div class="main-container">
    <div class="budget-container">
        <h2>Set New Password</h2>
        <form action="../../App/Controller/UserController.php" method="post" onsubmit="validateForm(event)"
              class="form-container">
            <div>
                <input type="password" name="previousPassword" placeholder="Enter old password"
                       value="<?= $previous_password ?>" id="previous-pass">
                <div id="previousPasswordError" class="error"></div>
                <?php
                if (isset($_SESSION['incorrect-password'])) {
                    echo "<div class='error'>" . $_SESSION['incorrect-password'] . "</div>";
                    unset($_SESSION['incorrect-password']);
                }
                ?>
            </div>

            <div>
                <input type="password" name="newPassword" placeholder="New password" value="<?= $new_password ?>"
                       id="new-pass">
                <div id="newPasswordError" class="error"></div>
            </div>

            <div>
                <input type="password" name="confirmPassword" placeholder="Confirm password"
                       value="<?= $confirm_password ?>" id="confirm-pass">
                <div id="confirmPasswordError" class="error"></div>
                <?php
                if (isset($_SESSION['no-match-error'])) {
                    echo "<div class='error'>" . $_SESSION['no-match-error'] . "</div>";
                    unset($_SESSION['no-match-error']);
                }
                ?>
            </div>

            <div>
                <button type="submit" name="submit" value="changePassword">Change Password</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
