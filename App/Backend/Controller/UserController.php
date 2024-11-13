<?php
include_once "../Database/UserDB.php";

class userController
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
            header("location: ../../Frontend/Markup/register.php?error=" .
                urlencode("the username is already taken"));
            exit();
        }
        if ($user->register_user($first_name, $last_name, $user_name, $hashed_password)) {
            echo "sucess";
        } else {
            echo "error";
        }
    }

    public function login_user()
    {
        $user_name = htmlspecialchars($_POST['userName']);
        $password = htmlspecialchars($_POST['password']);
        $hashed_password = hash('sha256', $password);
        $user = new UserDB();
        if ($user->is_username_available($user_name)) {
            echo "username doesnot exist";
            exit();
        }
        if ($user->login_user($user_name, $hashed_password)) {
            session_start();
            $_SESSION["userName"] = $user_name;
            header('Location: ../../Frontend/Markup/dashboard.php');

        }
    }
}

$user = new UserController();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $user->login_user();
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $user->register_user();
    exit();
}
