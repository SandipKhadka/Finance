<?php
require_once __DIR__ . '/../Database/ExpensesDB.php';
require_once __DIR__ . '/../Database/SpendingLimitDB.php';

session_start();

class SpendLimitController
{
    public function get_expenses_category()
    {
        $user_name = $_SESSION['userName']; // Access session variable

        $expenses = new ExpensesDB();
        return $expenses->get_expenses_category($user_name);
    }

    public function add_spend_limit()
    {
        $amount = $_POST['amount'];
        $category_id = $_POST['category-id'];
        $user_name = $_SESSION['userName'];

        $spend_limit_db = new SpendingLimitDB();
        $spend_limit_db->add_or_update_spend_limit($user_name, $amount, $category_id);
    }

    public function get_all_spend_limit()
    {
        $user_name = $_SESSION['userName'];
        $spend_limit_db = new SpendingLimitDB();
        $spend_limit_data = $spend_limit_db->get_spending_limit($user_name);
        return $spend_limit_data;
    }

    public function close_db_connection()
    {
        DBConnection::close_connection();
    }
}

if (!isset($_SESSION['userName'])) {
    header('location: /finance');
    die;
}

$spend_limit = new SpendLimitController();
if (isset($_POST['submit']) && $_POST['submit'] == "add") {
    $spend_limit->add_spend_limit();
    header('location: ../../Public/Markup/dashboard.php');
}
