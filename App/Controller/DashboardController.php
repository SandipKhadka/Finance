<?php
require_once __DIR__ . '/../Database/IncomeDB.php';

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
}

$dashboard = new DashboardController();
$income = $dashboard->get_income();
$_SESSION['income'] = $income;

?>