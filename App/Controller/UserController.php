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
            header("location: ../../Public/Markup/register.php");
            $_SESSION['entered-first-name'] = $first_name;
            $_SESSION['entered-last-name'] = $last_name;
            $_SESSION['entered-username'] = $user_name;
            $_SESSION['error'] = "Username already taken";
            exit();
        }
        if ($user->register_user($first_name, $last_name, $user_name, $hashed_password)) {
            $_SESSION['userName'] = $user_name;
            header('Location: ../../Public/Markup/dashboard.php');
        }
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

    public function logout_user()
    {
        setcookie("userName", "", time() - 3600, "/");
        session_unset();
        session_destroy();
        header('location: /phpfinance');
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

if (isset($_SESSION['userName'])) {
    header('Location: /phpfinance/Public/Markup/dashboard.php');
    die;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $user->login_user();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $user->register_user();
    exit();
}

