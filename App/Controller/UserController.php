<?php

require_once __DIR__ . '/../Database/UserDB.php';

session_start();

class UserController
{
    public function register_user()
    {
        $first_name = htmlspecialchars($_POST['firstName']);
        $last_name = htmlspecialchars($_POST['lastName']);
        $user_name = htmlspecialchars($_POST['userName']);
        $password = htmlspecialchars($_POST['password']);
        $hashed_password = hash('sha256', $password);

        $user = new UserDB();
        if (!$user->is_username_available($user_name)) {
            $_SESSION['entered-first-name'] = $first_name;
            $_SESSION['entered-last-name'] = $last_name;
            $_SESSION['entered-username'] = $user_name;
            $_SESSION['username-error'] = "Username already taken";
            header("location: ../../Public/Markup/register.php");
            exit();
        }
        if ($user->register_user($first_name, $last_name, $user_name, $hashed_password)) {
            $_SESSION['userName'] = $user_name;
            header('Location: ../../Public/Markup/dashboard.php');
        }
    }

    public function logout_user()
    {
        setcookie("userName", "", time() - 3600, "/");
        session_unset();
        session_destroy();
        header('location: /finance');
    }

    public function change_password()
    {
        $user_name = $_SESSION['userName'];
        $previous_password = htmlspecialchars($_POST['previousPassword']);
        $new_password = htmlspecialchars($_POST['newPassword']);
        $confirm_password = htmlspecialchars($_POST['confirmPassword']);

        $hashed_password = hash('sha256', $previous_password);

        $userDB = new UserDB();

        if (!$userDB->login_user($user_name, $hashed_password)) {
            $_SESSION['entered-previous-password'] = $previous_password;
            $_SESSION['incorrect-password'] = "The password is incorrect";
            header("location: ../../Public/Markup/change_password.php");
            exit();
        }
        if ($new_password != $confirm_password) {
            $_SESSION['entered-new-password'] = $new_password;
            $_SESSION['entered-confirm-password'] = $confirm_password;
            $_SESSION['no-match-error'] = "Passwords do not match";
            header("location: ../../Public/Markup/change_password.php");
            exit();
        }
        $hashed_password  = hash('sha256', $new_password);
        $userDB = new UserDB();
        $userDB->change_password($user_name, $hashed_password);
        header("location: ../../Public/Markup/dashboard.php");
  }

    public function login_user()
    {
        $user_name = htmlspecialchars($_POST['userName']);
        $password = htmlspecialchars($_POST['password']);
        $hashed_password = hash('sha256', $password);
        $user = new UserDB();
        if ($user->is_username_available($user_name)) {
            $_SESSION['username-error'] = "The username is not available";
            header("Location: ../../index.php");
            exit();
        }
        if ($user->login_user($user_name, $hashed_password)) {
            $_SESSION["userName"] = $user_name;
            if (isset($_POST['remember'])) {
                setcookie("userName", $user_name, time() + (86400 * 30), "/");
            }
            header('Location: ../../Public/Markup/dashboard.php');
            exit();
        }
        $_SESSION['entered-username'] = $user_name;
        $_SESSION['password-error'] = "The password is incorrect";
        header("Location: ../../index.php");
    }

}

if (isset($_COOKIE['userName'])) {
    $_SESSION['userName'] = $_COOKIE['userName'];
}

$user = new UserController();

if (isset($_SESSION['userName']) && isset($_GET['submit']) && $_GET['submit'] == "logout") {
    $user->logout_user();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $user->login_user();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $user->register_user();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit']) && $_POST['submit'] == "changePassword") {
    $user->change_password();
    exit();
}

if (isset($_SESSION['userName'])) {
    header('Location: /finance/Public/Markup/dashboard.php');
    die;
}