<?php
require_once __DIR__ . '/../Database/BackupDB.php';
session_start();
class BackupController {
    public function get_income_backup()
    {
        $user_name = $_SESSION['userName'];
        $backup = new BackupDB();
        $income_backup = $backup->get_income_backup_data($user_name);
        return $income_backup;
    }

    public function restore_income_backup()
    {
        $backup = new BackupDB();
        $income_id = $_POST['income-id'];
        $backup->backup_income_record($income_id);
    }

    public function get_expenses_backup()
    {
        $user_name = $_SESSION['userName'];
        $backup = new BackupDB();
        $expenses_backup = $backup->get_expenses_backup_data($user_name);
        return $expenses_backup;
    }

    public function restore_expenses_backup()
    {
        $backup = new BackupDB();
        $expenses_id = $_POST['expenses-id'];
        $backup->backup_expenses_record($expenses_id);
    }
}

$backup = new BackupController();
if(isset($_POST['submit']) && $_POST['submit'] == 'income-backup'){
    $backup->restore_income_backup();
}

if(isset($_POST['submit']) && $_POST['submit'] == 'expenses-backup'){
    $backup->restore_expenses_backup();
}