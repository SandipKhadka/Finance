<?php

require_once __DIR__ . '/../Database/IncomeDB.php';

session_start();

class IncomeController
{
    public function add_income()
    {
        $amount = htmlspecialchars($_POST['amount']);
        $category_id = htmlspecialchars($_POST['category-id']);
        $remarks = htmlspecialchars($_POST['remarks']);

        $user_name = $_SESSION['userName']; // Access session variable
        $income = new IncomeDB();
        $income->add_income($amount, $category_id, $remarks, $user_name);
        header("Location: ../../Public/Markup/income_transaction.php");
    }

    public function add_income_category()
    {
        $category_name = $_POST['category-name'];

        $user_name = $_SESSION['userName']; // Access session variable
        $income = new IncomeDB();
        $income->add_income_category($category_name, $user_name);
        header("Location: ../../Public/Markup/income_transaction.php");
    }

    public function get_income_category()
    {
        $user_name = $_SESSION['userName']; // Access session variable

        $income = new IncomeDB();
        return $income->get_income_category($user_name);
    }

    public function get_all_income_transaction($start_filter_date, $end_filter_date)
    {
        $user_name = $_SESSION['userName']; // Access session variable
        if ($start_filter_date == null) {
            $start_filter_date = date("Y-m");
        }
        $income = new IncomeDB();

        $all_income_transaction = $income->get_all_income_transaction($user_name, $start_filter_date, $end_filter_date);

        return $all_income_transaction;
    }

    public function get_piechart_data($start_filter_date, $end_filter_date)
    {
        $user_name = $_SESSION['userName']; // Access session variable
        if ($start_filter_date == null) {
            $start_filter_date = date("Y-m");
        }
        $income = new IncomeDB();
        $piechart_data = $income->get_income_with_amount_and_category($user_name, $start_filter_date, $end_filter_date);
        return $piechart_data;
    }

    public function get_linechart_data($start_filter_date, $end_filter_date)
    {
        $user_name = $_SESSION['userName']; // Access session variable
        if ($start_filter_date == null) {
            $start_filter_date = date("Y-m");
        }
        $income = new IncomeDB();
        $linegraph_data = $income->get_income_by_day($user_name, $start_filter_date, $end_filter_date);
        return $linegraph_data;
    }

    public function get_bargraph_data($start_filter_date, $end_filter_date)
    {
        $user_name = $_SESSION['userName']; // Access session variable
        if ($start_filter_date == null) {
            $start_filter_date = date("Y-m");
        }
        $income = new IncomeDB();
        $bargraph_data = $income->get_top_five_category($user_name, $start_filter_date, $end_filter_date);
        return $bargraph_data;
    }

    public function delete_income_transaction()
    {
        $income_id = $_POST['income-id'];
        $user_name = $_SESSION['userName']; // Access session variable
        $income = new IncomeDB();
        $income->delete_income_transaction($user_name, $income_id);
    }

    public function update_income_transaction()
    {
        $income_id = $_POST['income-id'];
        $amount = $_POST['amount'];
        $category_id = $_POST['categoryId'];
        $remarks = $_POST['remarks'];

        $user_name = $_SESSION['userName']; // Access session variable
        $income = new IncomeDB();
        $income->update_income_transaction($user_name, $amount, $remarks, $category_id, $income_id);
    }

    public function close_db_connection()
    {
        DBConnection::close_connection();
    }
}

// No need to call session_start() inside each method anymore
$income = new IncomeController();

if (isset($_POST['submit']) && $_POST['submit'] == 'add-income') {
    $income->add_income();
}

if (isset($_POST['submit']) && $_POST['submit'] == 'add-category') {
    $income->add_income_category();
}

if (isset($_POST['submit']) && $_POST['submit'] == 'filter') {
    $start_filter_date = isset($_POST['startFilterDate']) ? $_POST['startFilterDate'] : null;
    $end_filter_date = isset($_POST['endFilterDate']) ? $_POST['endFilterDate'] : null;
    $transaction = $income->get_all_income_transaction($start_filter_date, $end_filter_date);
    $piechart_data = $income->get_piechart_data($start_filter_date, $end_filter_date);
    $linegraph_data = $income->get_linechart_data($start_filter_date, $end_filter_date);
    $bargraph_data  = $income->get_bargraph_data($start_filter_date, $end_filter_date);

    $_SESSION['transaction'] = $transaction;
    $_SESSION['piechart_data'] = $piechart_data;
    $_SESSION['linegraph_data'] = $linegraph_data;
    $_SESSION['bargraph_data'] = $bargraph_data;

    header("Location: ../../Public/Markup/income_transaction.php");
}

if (isset($_POST['submit']) && $_POST['submit'] == 'delete') {
    $income->delete_income_transaction();
    header('Location: ../../Public/Markup/income_transaction.php');
}

if (isset($_POST['submit']) && $_POST['submit'] == 'update') {
    $income->update_income_transaction();
    header('Location: ../../Public/Markup/income_transaction.php');
}
