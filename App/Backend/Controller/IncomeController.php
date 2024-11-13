<?php
require "../Database/IncomeDB.php";
class IncomeController
{
    public function add_income()
    {
        $amount = htmlspecialchars($_POST['amount']);
        $category_id = htmlspecialchars($_POST['category_id']);
        $remarks = htmlspecialchars($_POST['remarks']);

        session_start();
        $user_name = $_SESSION['userName'];
        $income = new IncomeDB();
        $income->add_income($amount, $category_id, $remarks, $user_name);
    }

    public function add_income_category()
    {
        $category_name = $_POST['category-name'];
        session_start();
        $user_name = $_SESSION['userName'];
        $income = new IncomeDB();
        $income->add_income_category($category_name, $user_name);
    }

    public function get_income_category()
    {
        session_start();
        $user_name = $_SESSION['userName'];

        $income = new IncomeDB();
        return $income->get_income_category($user_name);
    }
}

$income = new IncomeController();
if (isset($_POST['submit']) && $_POST['submit'] == 'add-income') {
    $income->add_income();
}

if (isset($_POST['submit']) && $_POST['submit'] == 'add-category') {
    $income->add_income_category();
}