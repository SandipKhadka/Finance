<?php

require_once __DIR__ . '/../Config/DBConnection.php';

class UserDB
{
    private $connection;

    public function __construct()
    {
        $this->connection = DBConnection::get_connection();  // Open the connection once
    }

    public function __destruct()
    {
        DBConnection::close_connection(); // Close the connection when the object is destroyed
    }

    public function register_user($first_name, $last_name, $user_name, $hashed_password)
    {
        $insert_status = false;
        $sql = "INSERT INTO user_details (first_name, last_name, user_name, password)" .
            "VALUES (?, ?, ?, ?)";
        $statement = mysqli_stmt_init($this->connection);

        if (!mysqli_stmt_prepare($statement, $sql)) {
            echo "Database query preparation failed!";
        } else {
            mysqli_stmt_bind_param($statement, "ssss", $first_name, $last_name, $user_name, $hashed_password);
            mysqli_stmt_execute($statement);
            $insert_status = true;
        }

        return $insert_status;
    }

    public function is_username_available($username)
    {
        $user_name_status = true;
        $sql = "SELECT COUNT(user_id) FROM user_details WHERE user_name = ?";
        $statement = mysqli_stmt_init($this->connection);

        if (!mysqli_stmt_prepare($statement, $sql)) {
            echo "Database query preparation failed!";
        } else {
            mysqli_stmt_bind_param($statement, "s", $username);
            mysqli_stmt_execute($statement);
            mysqli_stmt_bind_result($statement, $count);
            mysqli_stmt_fetch($statement);
            if ($count > 0) {
                $user_name_status = false;
            }
        }

        return $user_name_status;
    }

    public function login_user($username, $hashed_password)
    {
        return $this->is_password_correct($username, $hashed_password);
    }

    public function is_password_correct($username, $hashed_password)
    {
        $correctness = false;
        $sql = "SELECT COUNT(user_id) FROM user_details" .
            " WHERE user_name = ? AND password = ?";
        $statement = mysqli_stmt_init($this->connection);

        if (!mysqli_stmt_prepare($statement, $sql)) {
            echo "Database query preparation failed!";
        } else {
            mysqli_stmt_bind_param($statement, "si", $username, $hashed_password);
            mysqli_stmt_execute($statement);
            mysqli_stmt_bind_result($statement, $count);
            mysqli_stmt_fetch($statement);
            if ($count > 0) {
                $correctness = true;
            }
        }

        return $correctness;
    }
}
