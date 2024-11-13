<?php
require "../Config/DBConnection.php";

class IncomeDB
{
    private $connection;

    public function __construct()
    {
        $this->connection = DBConnection::get_connection();  // Open the connection once
    }

    public function __destruct()
    {
        DBConnection::close_connection();
    }

    public function add_income($amount, $category_id, $remarks, $user_name)
    {
        $sql = "INSERT INTO income(income_amount, income_category, user_id, remarks, date, time) " .
            "VALUES(?,?,?,?,CURDATE(),CURTIME())";
        $user_id = $this->get_user_id($user_name);
        echo $user_id;

        //test

        $category_id = 1;
        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iiis", $amount, $category_id, $user_id, $remarks);
            mysqli_stmt_execute($statement);
        }
    }

    public function get_user_id($user_name)
    {
        $user_id = null;
        $sql = "SELECT user_id FROM user_details WHERE user_name = ?";
        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "s", $user_name);
            mysqli_stmt_execute($statement);
            mysqli_stmt_bind_result($statement, $user_id);
            if (mysqli_stmt_fetch($statement)) {
                return $user_id;
            }
        }
        return null;
    }

    public function add_income_category($category_name, $user_name)
    {
        $user_id = $this->get_user_id($user_name);
        $sql = "INSERT INTO income_category(category_name,user_id) " .
            "VALUES(?,?)";
        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "si", $category_name, $user_id);
            mysqli_stmt_execute($statement);
        }
    }

    public function get_income_category($user_name)
    {
        $data = [];
        $user_id = $this->get_user_id($user_name);
        $sql = "SELECT * FROM income_category " .
            "WHERE user_id=?" .
            "OR user_id IS NULL";

        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "i", $user_id);
            mysqli_stmt_execute($statement);
            $result = mysqli_stmt_get_result($statement);
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }
}