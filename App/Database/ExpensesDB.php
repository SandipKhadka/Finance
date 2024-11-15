<?php
require_once __DIR__ . '/../Config/DBConnection.php';

class ExpensesDB
{
    private $connection;

    public function __construct()
    {
        $this->connection = DBConnection::get_connection();  // Open the connection once
    }

    public function add_expenses($amount, $category_id, $remarks, $user_name)
    {
        $sql = "INSERT INTO expenses(expenses_amount, expenses_category, user_id, remarks, date, time) " .
            "VALUES(?,?,?,?,CURDATE(),CURTIME())";
        $user_id = $this->get_user_id($user_name);

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

    public function get_expenses_amount($user_name)
    {
        $user_id = $this->get_user_id($user_name);
        $sql = "SELECT " .
            "SUM(expenses_amount) " .
            "FROM expenses " .
            " WHERE user_id=? AND YEAR(date) =? AND MONTH(date) =?";
        $expenses = 0;

        $date = date("Y-m");
        list($year, $month) = explode('-', $date);
        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iii", $user_id, $year, $month);
            mysqli_stmt_execute($statement);
            $result = mysqli_stmt_get_result($statement);
            $row = mysqli_fetch_assoc($result);
            $expenses = $row["SUM(expenses_amount)"];
        }
        return $expenses;
    }


    public function add_expenses_category($category_name, $user_name)
    {
        $user_id = $this->get_user_id($user_name);
        $sql = "INSERT INTO expenses_category(category_name,user_id) " .
            "VALUES(?,?)";
        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "si", $category_name, $user_id);
            mysqli_stmt_execute($statement);
        }
    }

    public function get_expenses_category($user_name)
    {
        $data = [];
        $user_id = $this->get_user_id($user_name);
        $sql = "SELECT category_name,category_id FROM expenses_category " .
            "WHERE user_id=? " .
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

    public function get_all_expenses_transaction($user_name, $start_filter_date, $end_filter_date)
    {
        $user_id = $this->get_user_id($user_name);
        $data = [];

        $sql = "SELECT " .
            "expenses_id, expenses_amount," .
            "category_name,remarks" .
            ",date," .
            "time , " .
            "expenses.expenses_category " .
            "FROM expenses " .
            "INNER JOIN expenses_category " .
            "ON expenses.expenses_category=expenses_category.category_id " .
            "WHERE (expenses.user_id =? AND YEAR(date) >=? AND MONTH(date) >=? AND YEAR(date) <=? AND MONTH(date) <=?)" .
            "OR (expenses.user_id=? AND YEAR(date)=? AND MONTH(date)=?)";
        $statement = mysqli_stmt_init($this->connection);
        list($start_filter_year, $start_filter_month) = explode('-', $start_filter_date);
        if ($end_filter_date != NULL) {
            list($end_filter_year, $end_filter_month) = explode('-', $end_filter_date);
        }
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iiiiiiii",
                $user_id, $start_filter_year, $start_filter_month,
                $end_filter_year, $end_filter_month,
                $user_id, $start_filter_year, $start_filter_month);
            mysqli_stmt_execute($statement);
            $result = mysqli_stmt_get_result($statement);
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function delete_expenses_transaction($user_name, $expenses_id)
    {
        $user_id = $this->get_user_id($user_name);
        $sql = "DELETE FROM expenses " .
            "WHERE expenses_id=?";
        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "i", $expenses_id);
            mysqli_stmt_execute($statement);

        }
    }

    public function update_expenses_transaction($user_name, $amount, $remarks, $category_id, $expenses_id)
    {
        $user_id = $this->get_user_id($user_name);
        $sql = "UPDATE expenses SET expenses_amount=? ,expenses_category=?, remarks=?" .
            " WHERE user_id=? AND expenses_id=?";
        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iisii", $amount, $category_id, $remarks, $user_id, $expenses_id);
            mysqli_stmt_execute($statement);
        }
    }

    public function get_expenses_with_amount_and_category($user_name, $start_filter_date, $end_filter_date)
    {
        $user_id = $this->get_user_id($user_name);
        $data = [];
        $sql = "SELECT " .
            "SUM(expenses.expenses_amount)," .
            "expenses_category.category_name " .
            "FROM expenses" .
            " INNER JOIN expenses_category ON " .
            "expenses.expenses_category = expenses_category.category_id" .
            " WHERE (expenses.user_id=? AND YEAR(date)>=? AND MONTH(date)>=? AND YEAR(date)<=? AND MONTH(date)<=?)" .
            "OR (expenses.user_id=? AND YEAR(date)=? AND MONTH(date)=?) " .
            "GROUP BY expenses_category.category_id";
        $statement = mysqli_stmt_init($this->connection);
        list($start_filter_year, $start_filter_month) = explode('-', $start_filter_date);
        if ($end_filter_date != NULL) {
            list($end_filter_year, $end_filter_month) = explode('-', $end_filter_date);
        }
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iiiiiiii",
                $user_id, $start_filter_year, $start_filter_month,
                $end_filter_year, $end_filter_month,
                $user_id, $start_filter_year, $start_filter_month
            );
            mysqli_stmt_execute($statement);
            $result = mysqli_stmt_get_result($statement);
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function get_expenses_by_day($user_name, $start_filter_date, $end_filter_date)
    {
        $user_id = $this->get_user_id($user_name);
        $data = [];
        $sql = "SELECT " .
            "DAY(date)," .
            "SUM(expenses.expenses_amount) " .
            "FROM expenses  " .
            "WHERE (user_id=? AND YEAR(date)>=? AND MONTH(date)<=? AND YEAR(date)<=? AND MONTH(date)<=?)" .
            "OR (user_id=? AND YEAR(date)=? AND MONTH(date)=?) " .
            "GROUP BY DAY(date) ";
        $statement = mysqli_stmt_init($this->connection);
        list($start_filter_year, $start_filter_month) = explode('-', $start_filter_date);
        if ($end_filter_date != NULL) {
            list($end_filter_year, $end_filter_month) = explode('-', $end_filter_date);
        }
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iiiiiiii",
                $user_id, $start_filter_year, $start_filter_month,
                $end_filter_year, $end_filter_month,
                $user_id, $start_filter_year, $start_filter_month);
            mysqli_stmt_execute($statement);
            $result = mysqli_stmt_get_result($statement);
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }


    public function get_top_five_category($user_name, $start_filter_date, $end_filter_date)
    {
        $user_id = $this->get_user_id($user_name);
        $data = [];
        $sql = "SELECT " .
            "SUM(expenses.expenses_amount)," .
            "expenses_category.category_name " .
            "FROM expenses " .
            "INNER JOIN expenses_category " .
            "ON expenses.expenses_category = expenses_category.category_id" .
            " WHERE( expenses.user_id=? AND YEAR(DATE)>=? AND MONTH(DATE)>=? AND YEAR(date)<=? AND MONTH(date)<=?)" .
            "OR (expenses.user_id=? AND YEAR(date)=? AND MONTH(date)=?)" .
            " GROUP BY expenses.expenses_category" .
            " ORDER BY SUM(expenses.expenses_amount) DESC LIMIT 5";

        $statement = mysqli_stmt_init($this->connection);
        list($start_filter_year, $start_filter_month) = explode('-', $start_filter_date);
        if ($end_filter_date != NULL) {
            list($end_filter_year, $end_filter_month) = explode('-', $end_filter_date);
        }
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iiiiiiii",
                $user_id, $start_filter_year, $start_filter_month,
                $end_filter_year, $end_filter_month,
                $user_id, $start_filter_year, $start_filter_month);
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
