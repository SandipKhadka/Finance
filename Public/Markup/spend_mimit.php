<?php
require_once __DIR__ . '/../../App/Controller/SpendLimitController.php';
$spend_limit_controller = new SpendLimitController();
$expenses_category = $spend_limit_controller->get_expenses_category();

$spend_limit_data = $spend_limit_controller->get_all_spend_limit();
$spend_limit_controller->close_db_connection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="../CSS/dashboard.css"/>
    <title>Spending Limit</title>
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
                const dropdown = document.getElementById('profile-dropdown');
                if (dropdown.style.display === 'block') {
                    dropdown.style.display = 'none';
                } else {
                    dropdown.style.display = 'block';
                }
            }

            window.onclick = function(event) {
                const dropdown = document.getElementById('profile-dropdown');
                if (!event.target.closest('.profile-container')) {
                    dropdown.style.display = 'none';
                }
            }
        </script>
    </div>
<div class="main-container">
<div class="budget-container">
    <h1>Set Spending Limit</h1>
    <table class="budget-table">
        <tr>
            <th>Limit</th>
            <th>Category</th>
            <th>Spent Amount</th>
            <th>Left Amount</th>
        </tr>
        <?php
        foreach ($spend_limit_data as $spend_limit) {
            echo "<tr>";
            echo "<td>" . $spend_limit['amount'] . "</td>";
            echo "<td>" . $spend_limit['category_name'] . "</td>";
            echo "<td>" . $spend_limit['SUM(expenses.expenses_amount)'] . "</td>";
            $left_limit = $spend_limit['amount'] - $spend_limit['SUM(expenses.expenses_amount)'];
            echo "<td>" . $left_limit . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <form action="../../App/Controller/SpendLimitController.php" method="post" class="form-container">
        <input
                type="text"
                name="amount"
                placeholder="Enter the spending limit"
                required
        />
        <select name="category-id" id="category-id" required>
            <option value="">Select Category</option>

            <?php
            foreach ($expenses_category as $category) {
                echo '<option value="' . $category['category_id'] . '">' . $category['category_name'] . '</option>';
            }
            ?>
        </select>
        <button type="submit" name="submit" value="add">
            Add spending limit
        </button>
    </form>
</div>
</div>
</body>
</html>