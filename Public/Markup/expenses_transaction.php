<?php
require_once __DIR__ . '/../../App/Controller/ExpensesController.php';

$expenses_controller = new ExpensesController();
$expenses_category = $expenses_controller->get_expenses_category();

$expenses_transaction = [];
if (isset($_SESSION['transaction'])) {
    $expenses_transaction = $_SESSION['transaction'];
    unset($_SESSION['transaction']);
} else {
    $expenses_transaction = $expenses_controller->get_all_expenses_transaction(null, null);
}


$piechart_data = [];
if (isset($_SESSION['piechart_data'])) {
    $piechart_data = $_SESSION['piechart_data'];
    unset($_SESSION['piechart_data']);
} else {
    $piechart_data = $expenses_controller->get_pie_chart_data(null, null);
}

$linegraph_data = [];
if (isset($_SESSION['linegraph_data'])) {
    $linegraph_data = $_SESSION['linegraph_data'];
    unset($_SESSION['linegraph_data']);
} else {
    $linegraph_data = $expenses_controller->get_line_chart(null, null);
}

$bargraph_data = [];
if (isset($_SESSION['bargraph_data'])) {
    $bargraph_data = $_SESSION['bargraph_data'];
    unset($_SESSION['bargraph_data']);
} else {
    $bargraph_data = $expenses_controller->get_bar_graph_data(null, null);
}

$expenses_controller->close_db_connection();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <title>Expenses</title>
    <script src="../js/button_hide.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load("current", { packages: ["corechart"] });

        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn("string", "Category");
            data.addColumn("number", "Amount");

            <?php
            if (!empty($piechart_data)) {
                foreach ($piechart_data as $pie) {
                    echo "data.addRow(['" . addslashes($pie['category_name']) . "', " . $pie['SUM(expenses.expenses_amount)'] . "]);\n";
                }
            } else {
                echo "console.log('No piechart data available');\n";  // Add debug if no data
            }
            ?>

            var options = {
                title: "Expenses of this Month",
                titleTextStyle: {
                    color: 'black',
                    bold: true
                },
                width: 600,
                height: 400,
                backgroundColor: "#e6e6e6"
            };

            var chart = new google.visualization.PieChart(document.getElementById("expenses-by-group"));

            if (data.getNumberOfRows() > 0) {
                chart.draw(data, options);
            } else {
                console.log('Pie chart data is empty or invalid');
            }
        }
    </script>

    <script type="text/javascript">
        google.charts.load("current", { packages: ["line"] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn("number", "Day");
            data.addColumn("number", "Amount");
            <?php
            foreach ($linegraph_data as $line) {
                echo "data.addRows([[" . addslashes($line['DAY(date)']) . ", " . $line['SUM(expenses.expenses_amount)'] . "]]);\n";
            }
            ?>
            var options = {
                chart: {
                    title: "Expenses By Day",
                },
                titleTextStyle: {
                    color: 'black',
                    bold: true
                },
                width: 600,
                height: 400,
                backgroundColor: "#e6e6e6"
            };

            var chart = new google.charts.Line(
                document.getElementById("expenses-by-day")
            );

            chart.draw(data, google.charts.Line.convertOptions(options));
        }
    </script>

    <script type="text/javascript">
        google.charts.load("current", { packages: ["corechart"] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            // Start with a static header row for the chart
            var data = google.visualization.arrayToDataTable([
                ["Category Name", "TotalSpend", { role: "style" }]
                <?php
                foreach ($bargraph_data as $bar) {
                    $category_name = addslashes($bar['category_name']);
                    $total_spend = $bar['SUM(expenses.expenses_amount)'];
                    $color = 'blue'; // You can customize this based on your data if needed
                    echo ",['$category_name', $total_spend, '$color']"; // This adds a row for each bar
                }
                ?>
            ]);

            var options = {
                title: "Expenses By Category",
                titleTextStyle: { color: 'black', bold: true },
                width: 600,
                height: 400,
                bar: { groupWidth: "95%" },
                legend: { position: "none" },
                backgroundColor: "#e6e6e6",
            };

            var chart = new google.visualization.BarChart(document.getElementById("top-expenses-categories"));
            chart.draw(data, options);
        }
    </script>

</head>

<body>
    <div class="container">
        <a href="dashboard.php">
            <button>&#x2190; Dashboard</button>
        </a>
        <div class="ie-form">
            <form action="../../App/Controller/ExpensesController.php" method="post">
                <h2>Add New Transaction</h2>
                <input type="number" name="amount" placeholder="Enter amount" required />
                <select name="category-id" id="category-id" required>
                    <option selected disabled value="">Select category</option>
                    <?php
                    foreach ($expenses_category as $category) {
                        echo '<option value="' . $category['category_id'] . '">' . $category['category_name'] . '</option>';
                    }
                    ?>
                </select>
                <input type="text" name="remarks" placeholder="Enter expenses remarks" required />
                <button type="submit" name="submit" value="add-expenses">
                    Add expenses
                </button>
            </form>

            <div>
                <?php
                if (isset($_SESSION["expenses_error"])) {
                    $error_message = $_SESSION["expenses_error"];
                    echo $error_message;
                    unset($_SESSION['expenses_error']);
                }
                ?>
            </div>

            <form action="../../App/Controller/ExpensesController.php" method="post">
                <h2>Add New Category</h2>
                <input type="text" name="category-name" placeholder="Enter new category" />
                <button type="submit" name="submit" value="add-category">
                    Add Category
                </button>
            </form>
        </div>

    <div class="transactions">
        <h2>Transactions</h2>
        <div class="filter-section">
            <form action="../../App/Controller/ExpensesController.php" method="post">
                <label for="startFilterDate">Start Date:</label>
                <input type="month" name="startFilterDate"
                    value="<?= isset($_POST['startFilterDate']) ? $_POST['startFilterDate'] : '' ?>" />

                <label for="endFilterDate">End Date:</label>
                <input type="month" name="endFilterDate"
                    value="<?= isset($_POST['endFilterDate']) ? $_POST['endFilterDate'] : '' ?>" />

                <button type="submit" name="submit" value="filter">Filter</button>
            </form>
        </div>
        <div>
            <span>${updateError}</span>
        </div>
        <table class="transactions-table">
            <tr>
                <th>Expenses Amount</th>
                <th>Category</th>
                <th>Remarks</th>
                <th>Date</td>
                <th>Time</td>
                <th>Action</th>
            </tr>
            <tr>
                <?php
                foreach ($expenses_transaction as $transaction) {
                    echo '<form action="../../App/Controller/ExpensesController.php" method="post">';
                    echo "<tr>";
                    echo "<td>" .
                        "<label for='amount" . $transaction['expenses_id'] . "' "
                        . "id='defaultAmount" . $transaction['expenses_id'] . "'>" . $transaction['expenses_amount'] . "</label>" .
                        "<input type='number' name='amount' "
                        . "id='amount-" . $transaction['expenses_id'] . "' "
                        . "value='" . $transaction['expenses_amount'] . "' hidden/>" .
                        "</td>";

                    echo "<td>" .
                        "<span id='defaultCategory" . $transaction['expenses_id'] . "'>" .
                        $transaction['category_name'] .
                        "</span>" .
                        "<select name='categoryId' id='categoryId-" . $transaction['expenses_id'] . "' hidden>" .
                        "<option selected value='" . $transaction['expenses_category'] . "'>" . $transaction['category_name'] . "</option>";

                    foreach ($expenses_category as $category) {
                        echo "<option value='" . $category['category_id'] . "'>" . $category['category_name'] . "</option>";
                    }

                    echo "</select>" .
                        "</td>";

                    echo "<td>" .
                        "<label for='remarks" . $transaction['expenses_id'] . "' "
                        . "id='defaultRemarks" . $transaction['expenses_id'] . "'>"
                        . $transaction['remarks'] . "</label>" .
                        "<input type='text' name='remarks' "
                        . "id='remarks-" . $transaction['expenses_id'] . "' "
                        . "value='" . $transaction['remarks'] . "' hidden/>" .
                        "</td>";

                    echo '<td>' . $transaction['date'] . '</td>';
                    echo '<td>' . $transaction['time'] . '</td>';

                    echo "<td>" .
                        "<input type='number' "
                        . "value='" . $transaction['expenses_id'] . "' hidden='hidden' "
                        . "name='expenses-id' id='id-" . $transaction['expenses_id'] . "'>" .
                        "<button type='submit' name='submit' "
                        . "id='delete-" . $transaction['expenses_id'] . "' "
                        . "value='delete'>Delete now</button>" .
                        "<button type='button' "
                        . "id='edit-" . $transaction['expenses_id'] . "' "
                        . "onclick='edit(" . $transaction['expenses_id'] . ")'>Edit</button>" .
                        "<button type='submit' name='submit' "
                        . "id='update-" . $transaction['expenses_id'] . "' "
                        . "value='update' hidden='hidden'>Update Now</button>" .
                        "<button type='button' "
                        . "id='back-" . $transaction['expenses_id'] . "' hidden='hidden' "
                        . "onclick='back(" . $transaction['expenses_id'] . ")'>Back</button>" .
                        "</td>";

                    echo "</tr>";
                    echo "</form>";
                }
                ?>
            </tr>
        </table>
        
        <h2>Charts</h2>
        <div class="charts">
        <div class="chart" id="top-expenses-categories">
            <!-- Placeholder for Top 5 Expense Categories Chart -->
        </div>
        <div class="chart" id="expenses-by-group">
            <!-- Placeholder for Expense by Category Chart -->
        </div>
        <div class="chart" id="expenses-by-day"></div>
    </div>

    </div>
</body>

</html>