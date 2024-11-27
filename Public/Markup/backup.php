<?php
require_once __DIR__ . '/../../App/Controller/BackupController.php';

$backup = new BackupController();
$income_backup = $backup->get_income_backup();
$expenses_backup = $backup->get_expenses_backup();
$backup->close_db_connection();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>
</head>
<body>
<h1>Income backup</h1>
<table>
    <tr>
        <th>Category</th>
        <th>Amount</th>
        <th>Remarks</th>
        <th>Inserted Date</th>
        <th>Inserted Time</th>
        <th>Action</th>
    </tr>
    <?php
    if($income_backup ==null) {
        echo "<tr><td>No data found</td></tr>";
    } else {
        foreach ($income_backup as $income) {
            echo "<tr>";
            echo "<td>" . $income['category_name'] . "</td>";
            echo "<td>" . $income['income_amount'] . "</td>";
            echo "<td>" . $income['remarks'] . "</td>";
            echo "<td>" . $income['date'] . "</td>";
            echo "<td>" . $income['time'] . "</td>";
            echo "<td>";

            echo "<form method='post' action='../../App/Controller/BackupController.php'>";
            echo "<input type='number' value='" . $income['income_id'] . "' name='income-id' hidden>";
            echo "<button type='submit' name='submit' value='income-backup'>Backup Now</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
    }
    ?>
</table>

<h1>expenses backup</h1>
<table>
    <tr>
        <th>Category</th>
        <th>Amount</th>
        <th>Remarks</th>
        <th>Inserted Date</th>
        <th>Inserted Time</th>
        <th>Action</th>
    </tr>
    <?php
    if($expenses_backup ==null) {
        echo "<tr><td>No data found</td></tr>";
    } else {


        foreach ($expenses_backup as $expenses) {
            echo "<tr>";
            echo "<td>" . $expenses['category_name'] . "</td>";
            echo "<td>" . $expenses['expenses_amount'] . "</td>";
            echo "<td>" . $expenses['remarks'] . "</td>";
            echo "<td>" . $expenses['date'] . "</td>";
            echo "<td>" . $expenses['time'] . "</td>";
            echo "<td>";

            echo "<form method='post' action='../../App/Controller/BackupController.php'>";
            echo "<input type='number' value='" . $expenses['expenses_id'] . "' name='expenses-id' hidden>";
            echo "<button type='submit' name='submit' value='expenses-backup'>Backup Now</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
    }
    ?>
</table>


</body>
</html>
