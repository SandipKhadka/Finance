<?php
require_once __DIR__ . '/../Database/BackupDB.php';
session_start();

class BackupController
{
    public function get_income_backup()
    {
        $user_name = $_SESSION['userName'];
        $backup = new BackupDB();
        $backup_data = $backup->get_income_backup_data($user_name);
        return $backup_data;
    }

    public function restore_income_backup()
    {
        $backup = new BackupDB();
        $backup_id = $_POST['backup-id'];
        $backup->backup_income_record($backup_id);
    }

    public function get_expenses_backup()
    {
        $user_name = $_SESSION['userName'];
        $backup = new BackupDB();
        $backup_data = $backup->get_expenses_backup_data($user_name);
        return $backup_data;
    }

    public function restore_expenses_backup()
    {
        $backup = new BackupDB();
        $backup_id = $_POST['backup-id'];
        $backup->backup_expenses_record($backup_id);
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

$backup = new BackupController();
if (isset($_POST['submit']) && $_POST['submit'] == 'income-backup') {
    $backup->restore_income_backup();
    header("Location: ../../Public/Markup/backup.php");
}

if (isset($_POST['submit']) && $_POST['submit'] == 'expenses-backup') {
    $backup->restore_expenses_backup();
    header("Location: ../../Public/Markup/backup.php");
}