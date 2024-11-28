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
    <link rel="stylesheet" href="../CSS/income_transaction.css">
    <title>Backup & Restore</title>
</head>

<body>
    <div class="header-bar">
        <h1><a href="dashboard.php"> BudgetBuddy </a></h1>

        <div class="buttoncontainer">
            <div class="buttons">
                <a href="income_transaction.php">
                    <button class="dashboard-button">Income</button>
                </a>
                <a href="expenses_transaction.php">
                    <button class="dashboard-button">Expenses</button>
                </a>
                <a href="spend_mimit.php">
                    <button class="dashboard-button">Budget</button>
                </a><a href="backup.php">
                    <button class="dashboard-button">Backup</button>
                </a>
            </div>
        </div>

        <div class="profile-container">
            <div onclick="toggleDropdown()" class="profile-icon"> &#9679;
                <img src="../icons/user-icon.png" alt="User Profile" class="profile-img">
            </div>
            <div id="profile-dropdown" class="dropdown-content">
                <a href="../../App/Controller/UserController.php?submit=logout"> <img src="../icons/logout.png" class="icons"> Logout</a>
                <a href="change_password.php"> <img src="../icons/lock.png" class="icons"> Change Password</a>
            </div>
        </div>

        <script>
            function toggleDropdown() {
                document.getElementById('profile-dropdown').classList.toggle('show');
            }

            window.onclick = function(event) {
                if (!event.target.matches('.profile-icon')) {
                    var dropdown = document.getElementsByClassName('dropdown-content');
                    var i;
                    for (i = 0; i < dropdown.length; i++) {
                        var openDropdown = dropdown[i];
                        if (openDropdown.classList.contains('show')) {
                            openDropdown.classList.remove('show');
                        }
                    }
                }
            };
        </script>
    </div>

    <div class="container">
        <a href="dashboard.php">
            <button>&#x2190; Dashboard</button>
        </a>

        <h2>Income Backup</h2>
        <table class="transactions-table">
            <tr>
                <th>Category</th>
                <th>Amount</th>
                <th>Remarks</th>
                <th>Inserted Date</th>
                <th>Inserted Time</th>
                <th>Action</th>
            </tr>
            <?php
            if ($income_backup == null) {
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
                    echo "<input type='number' value='" . $income['backup_id'] . "' name='backup-id' hidden>";
                    echo "<button type='submit' name='submit' value='income-backup'>Backup Now</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </table>

        <h2>Expense Backup</h2>
        <table class="transactions-table">
            <tr>
                <th>Category</th>
                <th>Amount</th>
                <th>Remarks</th>
                <th>Inserted Date</th>
                <th>Inserted Time</th>
                <th>Action</th>
            </tr>
            <?php
            if ($expenses_backup == null) {
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
                    echo "<input type='number' value='" . $expenses['backup_id'] . "' name='backup-id' hidden>";
                    echo "<button type='submit' name='submit' value='expenses-backup'>Backup Now</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </table>
    </div>
</body>

</html>