<?php

require_once __DIR__ . '/../Database/ExpensesDB.php';
require_once __DIR__ . '/../Database/SpendingLimitDB.php';

session_start();

class ExpensesController
{

    public function add_expenses()
    {
        $amount = htmlspecialchars($_POST['amount']);
        $category_id = htmlspecialchars($_POST['category-id']);
        $remarks = htmlspecialchars($_POST['remarks']);

        $user_name = $_SESSION['userName'];

        $spendingLimitDB = new SpendingLimitDB();
        $spend_limit_amount = $spendingLimitDB->get_spend_limit_for_category($user_name, $category_id);

        $expenses = new ExpensesDB();
        $expenses_amount = $expenses->get_expenses_by_category($user_name, $category_id);

        $left_limit = $spend_limit_amount - $expenses_amount;

        if ($spend_limit_amount != 0) {
            if ($expenses_amount + (int) $amount >= $spend_limit_amount) {
                $_SESSION['expenses_error'] = "Expenses cannot be more than the spend limit the left amount is " . $left_limit;
                return;
            }
        }

        $expenses->add_expenses($amount, $category_id, $remarks, $user_name);
        header("Location: ../../Public/Markup/expenses_transaction.php");
    }

    public function add_expenses_category()
    {
        $category_name = $_POST['category-name'];

        $user_name = $_SESSION['userName']; // Access session variable
        $expenses = new ExpensesDB();
        $expenses->add_expenses_category($category_name, $user_name);
        header("Location: ../../Public/Markup/expenses_transaction.php");
    }

    public function get_expenses_category()
    {
        $user_name = $_SESSION['userName']; // Access session variable

        $expenses = new ExpensesDB();
        return $expenses->get_expenses_category($user_name);
    }

    public function get_all_expenses_transaction($start_filter_date, $end_filter_date)
    {
        $user_name = $_SESSION['userName']; // Access session variable
        if ($start_filter_date == null) {
            $start_filter_date = date("Y-m");
        }
        $expenses = new ExpensesDB();

        return $expenses->get_all_expenses_transaction($user_name, $start_filter_date, $end_filter_date);
    }

    public function delete_expenses_transaction()
    {
        $expenses_id = htmlspecialchars($_POST['expenses-id']);
        $user_name = $_SESSION['userName']; // Access session variable
        $expenses = new expensesDB();
        $expenses->delete_expenses_transaction($user_name, $expenses_id);
    }

    public function update_expenses_transaction()
    {
        $expenses_id = htmlspecialchars($_POST['expenses-id']);
        $amount = htmlspecialchars($_POST['amount']);
        $category_id = htmlspecialchars($_POST['categoryId']);
        $remarks = htmlspecialchars($_POST['remarks']);
        $user_name = $_SESSION['userName']; // Access session variable

        $expenses = new expensesDB();
        $original_expense = $expenses->get_original_expenses($user_name, $category_id, $expenses_id);
        $expenses_amount = $expenses->get_expenses_by_category($user_name, $category_id);

        $spendingLimitDB = new SpendingLimitDB();
        $spend_limit_amount = $spendingLimitDB->get_spend_limit_for_category($user_name, $category_id);

        $expenses_after_reducing_original = $expenses_amount - $original_expense;

        $left_limit = $spend_limit_amount - $expenses_after_reducing_original;

        if ($spend_limit_amount != 0) {
            if ($expenses_after_reducing_original + (int) $amount >= $spend_limit_amount) {
                $_SESSION['expenses_error'] = "Expenses cannot be more than the spend limit the left amount is " . $left_limit;
                return;
            }
        }
        $expenses->update_expenses_transaction($user_name, $amount, $remarks, $category_id, $expenses_id);
    }

    public function get_pie_chart_data($start_filter_date, $end_filter_date)
    {
        $user_name = $_SESSION['userName']; // Access session variable
        if ($start_filter_date == null) {
            $start_filter_date = date("Y-m");
        }
        $expenses = new expensesDB();
        return $expenses->get_expenses_with_amount_and_category($user_name, $start_filter_date, $end_filter_date);
    }

    public function get_line_chart($start_filter_date, $end_filter_date)
    {
        $user_name = $_SESSION['userName']; // Access session variable
        if ($start_filter_date == null) {
            $start_filter_date = date("Y-m");
        }
        $expenses = new expensesDB();
        return $expenses->get_expenses_by_day($user_name, $start_filter_date, $end_filter_date);
    }

    public function get_bar_graph_data($start_filter_date, $end_filter_date)
    {
        $user_name = $_SESSION['userName']; // Access session variable
        if ($start_filter_date == null) {
            $start_filter_date = date("Y-m");
        }
        $expenses = new expensesDB();
        return $expenses->get_top_five_category($user_name, $start_filter_date, $end_filter_date);
    }

    public function close_db_connection()
    {
        DBConnection::close_connection();
    }
}

if (!isset($_SESSION['userName'])) {
    header('location: /phpfinance');
    die;
}
$expenses = new ExpensesController();

if (isset($_POST['submit']) && $_POST['submit'] == 'add-expenses') {
    $expenses->add_expenses();
}

if (isset($_POST['submit']) && $_POST['submit'] == 'add-category') {
    $expenses->add_expenses_category();
}

if (isset($_POST['submit']) && $_POST['submit'] == 'filter') {
    $start_filter_date = isset($_POzST['startFilterDate']) ? $_POST['startFilterDate'] : null;
    $end_filter_date = isset($_POST['endFilterDate']) ? $_POST['endFilterDate'] : null;

    $transaction = $expenses->get_all_expenses_transaction($start_filter_date, $end_filter_date);
    $pie_chart_data = $expenses->get_pie_chart_data($start_filter_date, $end_filter_date);
    $line_graph_data = $expenses->get_line_chart($start_filter_date, $end_filter_date);
    $bar_graph_data = $expenses->get_bar_graph_data($start_filter_date, $end_filter_date);

    $_SESSION['transaction'] = $transaction;
    $_SESSION['piechart_data'] = $pie_chart_data;
    $_SESSION['linegraph_data'] = $line_graph_data;
    $_SESSION['bargraph_data'] = $bar_graph_data;

    header("Location: ../../Public/Markup/expenses_transaction.php");
}


if (isset($_POST['submit']) && $_POST['submit'] == 'delete') {
    $expenses->delete_expenses_transaction();
    header('Location: ../../Public/Markup/expenses_transaction.php');
}

if (isset($_POST['submit']) && $_POST['submit'] == 'update') {
    $expenses->update_expenses_transaction();
    header('Location: ../../Public/Markup/expenses_transaction.php');
}