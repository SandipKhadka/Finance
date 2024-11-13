<?php

function add_income() {
    $amount = $_POST['amount'];
    $category_id = $_POST['category_id'];
    $remarks = $_POST['remarks'];

    session_start();
    $user_name = $_SESSION['userName'];
    include "../Database/IncomeDB.php";
    $income = new IncomeDB();

    if(isset($_POST['submit']) && $_POST['submit'] == 'add-income') {
        echo "income added";
        echo $user_name;
        $income->add_income($amount, $category_id, $remarks, $user_name);
    }
    else {
        echo "income not added";
    }

}

if(isset($_POST['submit']) && $_POST['submit'] == 'add-income') {
    echo "income deleted";
    add_income();
}