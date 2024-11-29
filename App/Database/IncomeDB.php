<?php

require_once __DIR__ . '/../Config/DBConnection.php';

class IncomeDB
{
    private $connection;

    public function __construct()
    {
        $this->connection = DBConnection::get_connection();  // Open the connection once
    }


    public function add_income($amount, $category_id, $date, $remarks, $user_name)
    {
        $sql = "INSERT INTO income(income_amount, income_category, user_id, remarks, date, time) " .
            "VALUES(?,?,?,?,?,CURTIME())";
        $user_id = $this->get_user_id($user_name);

        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iiiss", $amount, $category_id, $user_id, $remarks, $date);
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

    public function get_income_amount($user_name)
    {
        $user_id = $this->get_user_id($user_name);
        $sql = "SELECT " .
            "SUM(income_amount) " .
            "FROM income " .
            " WHERE user_id=? AND YEAR(date) =? AND MONTH(date) =?";
        $income = 0;

        $date = date("Y-m");
        list($year, $month) = explode('-', $date);
        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iii", $user_id, $year, $month);
            mysqli_stmt_execute($statement);
            $result = mysqli_stmt_get_result($statement);
            $row = mysqli_fetch_assoc($result);
            $income = $row["SUM(income_amount)"];
        }
        return $income;
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
        $sql = "SELECT category_name,category_id FROM income_category " .
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

    public function get_all_income_transaction($user_name, $start_filter_date, $end_filter_date, $income_category)
    {
        $user_id = $this->get_user_id($user_name);
        $data = [];

        $start_year_month = date('Y-m', strtotime($start_filter_date));

        if ($end_filter_date == null) {
            $end_year_month = $start_year_month;
        } else {
            $end_year_month = date('Y-m', strtotime($end_filter_date));
        }

        $sql = "SELECT 
            income_id, income_amount, 
            category_name, remarks, 
            date, time, 
            income.income_category 
        FROM income 
        INNER JOIN income_category 
        ON income.income_category = income_category.category_id 
        WHERE income.user_id = ? 
        AND DATE_FORMAT(date, '%Y-%m') BETWEEN ? AND ? ";

        if ($income_category != null) {
            $sql .= " AND income.income_category = ?";
        }

        $statement = mysqli_stmt_init($this->connection);

        if (mysqli_stmt_prepare($statement, $sql)) {
            if ($income_category != null) {
                mysqli_stmt_bind_param($statement, "issi", $user_id, $start_year_month, $end_year_month, $income_category);
            } else {
                mysqli_stmt_bind_param($statement, "iss", $user_id, $start_year_month, $end_year_month);
            }

            mysqli_stmt_execute($statement);
            $result = mysqli_stmt_get_result($statement);

            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }

            return $data;
        }

        return null;
    }

    public function delete_income_transaction($user_name, $income_id)
    {
        $user_id = $this->get_user_id($user_name);
        $sql = "DELETE FROM income " .
            "WHERE income_id=?";
        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "i", $income_id);
            mysqli_stmt_execute($statement);

        }
    }

    public function update_income_transaction($user_name, $amount, $remarks, $category_id, $income_id)
    {
        $user_id = $this->get_user_id($user_name);
        $sql = "UPDATE income SET income_amount=? ,income_category=?, remarks=?" .
            " WHERE user_id=? AND income_id=?";
        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "iisii", $amount, $category_id, $remarks, $user_id, $income_id);
            mysqli_stmt_execute($statement);
        }
    }

    public function get_income_with_amount_and_category($user_name, $start_filter_date, $end_filter_date)
    {
        $user_id = $this->get_user_id($user_name);
        $data = [];
        $sql = "SELECT " .
            "SUM(income.income_amount)," .
            "income_category.category_name " .
            "FROM income" .
            " INNER JOIN income_category ON " .
            "income.income_category = income_category.category_id" .
            " WHERE (income.user_id=? AND YEAR(date)>=? AND MONTH(date)>=? AND YEAR(date)<=? AND MONTH(date)<=?)" .
            "OR (income.user_id=? AND YEAR(date)=? AND MONTH(date)=?) " .
            "GROUP BY income_category.category_id";
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

    public function get_income_by_day($user_name, $start_filter_date, $end_filter_date)
    {
        $user_id = $this->get_user_id($user_name);
        $data = [];
        $sql = "SELECT " .
            "DAY(date)," .
            "SUM(income.income_amount) " .
            "FROM income  " .
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
            "SUM(income.income_amount)," .
            "income_category.category_name " .
            "FROM income " .
            "INNER JOIN income_category " .
            "ON income.income_category = income_category.category_id" .
            " WHERE( income.user_id=? AND YEAR(DATE)>=? AND MONTH(DATE)>=? AND YEAR(date)<=? AND MONTH(date)<=?)" .
            "OR (income.user_id=? AND YEAR(date)=? AND MONTH(date)=?)" .
            " GROUP BY income.income_category" .
            " ORDER BY SUM(income.income_amount) DESC LIMIT 5";

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