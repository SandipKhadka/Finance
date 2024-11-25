<?php
require_once __DIR__ . '/../../App/Controller/SpendLimitController.php';
$spend_limit_controller = new SpendLimitController();
$expenses_category = $spend_limit_controller->get_expenses_category();

$spend_limit_data = $spend_limit_controller->get_all_spend_limit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="../CSS/spend_limit.css"/>
    <title>Spending Limit</title>
</head>

<body>
<div class="container">
    <a href="dashboard.php">
        <button>&#x2190; Dashboard</button>
    </a>
    <h1>Set Spending Limits</h1>
    <table class="transactions-table">
        <tr>
            <th>Limit</th>
            <th>Category</th>
        </tr>
        <?php
        foreach ($spend_limit_data as $spend_limit) {
            echo "<tr>";
            echo "<td>" . $spend_limit['amount'] . "</td>";
            echo "<td>" . $spend_limit['category_name'] . "</td>";
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
</body>
</html>