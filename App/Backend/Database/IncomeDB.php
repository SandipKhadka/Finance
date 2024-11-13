<?php
include_once "../Config/DBConnection.php";

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
        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iiis", $amount, $category_id,$user_id, $remarks);
            mysqli_stmt_execute($statement);
        }
    }

//    public function add_income_category(($category_name,$user_name)
//    {
//
//    }

    public function get_user_id($user_name) {
        $user_id = null;
        $sql = "SELECT user_id FROM user_details WHERE user_name = ?";
        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "s", $user_name);
            mysqli_stmt_execute($statement);
            mysqli_stmt_bind_result($statement, $user_id);
            if(mysqli_stmt_fetch($statement)) {
                return $user_id;
            }
        }
        return null;
    }
}