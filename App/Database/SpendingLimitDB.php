<?php

require_once __DIR__ . '/../Config/DBConnection.php';

class SpendingLimitDB
{
    public function __construct()
    {
        $this->connection = DBConnection::get_connection();  // Open the connection once
    }

    public function get_spend_limit_for_category($user_name, $category)
    {
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

    public function get_spending_limit($user_name)
    {
        // Get user_id based on the username
        $user_id = $this->get_user_id($user_name);

        // SQL Query with SUM and GROUP BY to aggregate expenses for each category
        $sql = "SELECT spending_limit.amount, 
                   expenses_category.category_name, 
                   SUM(expenses.expenses_amount) AS total_expenses
            FROM spending_limit
            INNER JOIN expenses_category ON spending_limit.category_id = expenses_category.category_id
            INNER JOIN expenses ON expenses_category.category_id = expenses.expenses_category
            WHERE spending_limit.user_id = ? 
              AND YEAR(spending_limit.date) = ? 
              AND MONTH(spending_limit.date) = ?
            GROUP BY spending_limit.amount, expenses_category.category_name";

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

            // Return the fetched data or an empty array if no data found
            return !empty($data) ? $data : null;
        }

        return null;
    }


    public function add_or_update_spend_limit($user_name, $amount, $category_id)
    {
        if ($this->does_spend_limit_already_exist($user_name, $category_id)) {
            $this->update_spend_limit($user_name, $amount, $category_id);
            return;
        }
        $this->add_spending_limit($user_name, $amount, $category_id);
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

}