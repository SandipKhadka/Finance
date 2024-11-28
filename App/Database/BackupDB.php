<?php

require_once __DIR__ . '/../Config/DBConnection.php';

class BackupDB
{
    private $connection;

    public function __construct()
    {
        $this->connection = DBConnection::get_connection();  // Open the connection once
    }

    public function get_income_backup_data($user_name)
    {
        $user_id = $this->get_user_id($user_name);
        $sql = "SELECT backup_id, income_id, income_category.category_name, income_amount,remarks,date,time  FROM income_backup
                INNER JOIN income_category ON income_backup.income_category = income_category.category_id
                    WHERE income_backup.user_id=?";
        $data = [];
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

    public function backup_income_record($backup_id)
    {
        $sql = "DELETE FROM income_backup WHERE backup_id= ? ";
        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "i", $backup_id);
            mysqli_stmt_execute($statement);
        }
    }

    public function get_expenses_backup_data($user_name)
    {
        $user_id = $this->get_user_id($user_name);
        $sql = "SELECT backup_id, expenses_id, expenses_category.category_name, expenses_amount,remarks,date,time  FROM expenses_backup
                INNER JOIN expenses_category ON expenses_backup.expenses_category = expenses_category.category_id
                    WHERE expenses_backup.user_id=?";
        $data = [];
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

    public function backup_expenses_record($expenses_id)
    {
        $sql = "DELETE FROM expenses_backup WHERE backup_id=?";
        $statement = mysqli_stmt_init($this->connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "i", $expenses_id);
            mysqli_stmt_execute($statement);
        }
    }

}