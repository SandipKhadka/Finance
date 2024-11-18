<?php
require_once __DIR__ . '/../Database/IncomeDB.php';
require_once __DIR__ . '/../Database/ExpensesDB.php';

session_start();


class DashboardController
{
    public function get_income()
    {
        if (!isset($_SESSION['userName'])) {
            header('Location: ../../index.php');
            exit;
        }
        $user_name = $_SESSION['userName'];
        $incomeDB = new IncomeDB();
        $income_amount = $incomeDB->get_income_amount($user_name);
        return $income_amount;
    }

    public function get_expenses()
    {
        if (!isset($_SESSION['userName'])) {
            header('Location: ../../index.php');
            exit;
        }
        $user_name = $_SESSION['userName'];
        $expensesDB = new ExpensesDB();
        $expenses_amount = $expensesDB->get_expenses_amount($user_name);
        return $expenses_amount;
    }

    public function get_chart_data()
    {
        $date = date("Y-m");
        $expenses = new ExpensesDB();

        $user_name = $_SESSION['userName'];

        $pie_chart_data = $expenses->get_expenses_with_amount_and_category($user_name, $date,null);
        $line_graph_data = $expenses->get_expenses_by_day($user_name, $date,null);
        $bar_graph_data = $expenses->get_top_five_category($user_name, $date,null);

        $_SESSION['piechart_data'] = $pie_chart_data;
        $_SESSION['linegraph_data'] = $line_graph_data;
        $_SESSION['bargraph_data'] = $bar_graph_data;
    }

    public function close_db_connection()
    {
        DBConnection::close_connection();
    }
}

if(!isset($_SESSION['userName'])) {
    header('location: /phpfinance');
    die;
}

$dashboard = new DashboardController();
$income = $dashboard->get_income();
$expenses = $dashboard->get_expenses();

$_SESSION['income'] = $income;
$_SESSION['expenses'] = $expenses;

$dashboard->get_chart_data();


?>