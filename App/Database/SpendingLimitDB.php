<?php

require_once __DIR__ . '/../Config/DBConnection.php';

class SpendingLimitDB
{
    public function __construct()
    {
        $this->connection = DBConnection::get_connection();  // Open the connection once
    }

    public function add_spending_limit($user_name, $amount, $category_id)
    {
        $user_id = $this->get_user_id($user_name);
        $sql = "INSERT INTO spending_limit(category_id, user_id, amount,date) " .
            "VALUES (?,?,?,CURDATE())";

        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iii", $category_id, $user_id, $amount);
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

    public function get_spend_limit_for_category($user_name,$category) {
        $user_id = $this->get_user_id($user_name);
        $sql = "SELECT amount FROM spending_limit WHERE user_id = ? AND category_id = ?";

        $amount = 0;

        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "ii", $user_id, $category);
            mysqli_stmt_execute($statement);
            mysqli_stmt_bind_result($statement, $amount);
            if (mysqli_stmt_fetch($statement)) {
                return $amount;
            }
        }
        return $amount;
    }

    public function get_spending_limit($user_name)
    {
        $user_id = $this->get_user_id($user_name);
        $sql = "SELECT " .
            "spending_limit." .
            "amount," .
            "expenses_category.category_name" .
            " FROM spending_limit" .
            " INNER JOIN expenses_category " .
            "ON spending_limit.category_id = expenses_category.category_id " .
            "WHERE spending_limit.user_id=? AND YEAR(date)=? AND MONTH(date)=?";

        $date = date("Y-m");
        list($year, $month) = explode("-", $date);
        $statement = mysqli_stmt_init($this->connection);
        $data = [];
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iii", $user_id, $year, $month);
            mysqli_stmt_execute($statement);
            $result = mysqli_stmt_get_result($statement);
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function update_spend_limit($user_name, $amount, $category_id)
    {
        $user_id = $this->get_user_id($user_name);
        $sql = "UPDATE spending_limit" .
            " SET amount=? " .
            "WHERE user_id=? AND category_id=? AND YEAR(date)=? AND MONTH(date)=? ";
        $date = date("Y-m");
        list($year, $month) = explode("-", $date);
        $statement = mysqli_stmt_init($this->connection);

        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iiiii", $amount, $user_id, $category_id, $year, $month);
            mysqli_stmt_execute($statement);
        }
    }

    public function does_spend_limit_already_exist($user_name, $category_id)
    {
        $user_id = $this->get_user_id($user_name);
        $sql = "SELECT id FROM spending_limit WHERE user_id = ? AND category_id = ? AND YEAR(date) = ? AND MONTH(date) = ?";

        $date = date("Y-m");
        list($year, $month) = explode("-", $date);

        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iiii", $user_id, $category_id, $year, $month);
            mysqli_stmt_execute($statement);
            mysqli_stmt_store_result($statement);

            if (mysqli_stmt_num_rows($statement) > 0) {
                return true;
            }
        }
        return false;
    }

    public function add_or_update_spend_limit($user_name, $amount, $category_id)
    {
        if($this->does_spend_limit_already_exist($user_name, $category_id)){
            $this->update_spend_limit($user_name, $amount, $category_id);
            return;
        }
        $this->add_spending_limit($user_name, $amount, $category_id);
    }

}