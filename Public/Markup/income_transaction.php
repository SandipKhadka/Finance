<?php
require_once __DIR__ . '/../../App/Controller/IncomeController.php';
$incomeController = new IncomeController();
$income_category = $incomeController->get_income_category();
$income_transaction = [];

if (isset($_SESSION['transaction'])) {
    $income_transaction = $_SESSION['transaction'];
    unset($_SESSION['transaction']);
} else {
    $income_transaction = $incomeController->get_all_income_transaction(null, null);
}

$piechart_data = [];
if (isset($_SESSION['piechart_data'])) {
    $piechart_data = $_SESSION['piechart_data'];
    unset($_SESSION['piechart_data']);
} else {
    $piechart_data = $incomeController->get_pie_chart_data(null, null);
}

$linegraph_data = [];
if (isset($_SESSION['linegraph_data'])) {
    $linegraph_data = $_SESSION['linegraph_data'];
    unset($_SESSION['linegraph_data']);
} else {
    $linegraph_data = $incomeController->get_line_chart(null, null);
}

$bargraph_data = [];
if (isset($_SESSION['bargraph_data'])) {
    $bargraph_data = $_SESSION['bargraph_data'];
    unset($_SESSION['bargraph_data']);
} else {
    $bargraph_data = $incomeController->get_bar_graph_data(null, null);
}

$incomeController->close_db_connection();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <script src="../js/button_hide.js"></script>
    <title>Income</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load("current", {
            packages: ["corechart"]
        });

        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn("string", "Category");
            data.addColumn("number", "Amount");

            <?php
            if (!empty($piechart_data)) {
                foreach ($piechart_data as $pie) {
                    echo "data.addRow(['" . addslashes($pie['category_name']) . "', " . $pie['SUM(income.income_amount)'] . "]);\n";
                }
            } else {
                echo "console.log('No piechart data available');\n";  // Add debug if no data
            }
            ?>

            var options = {
                title: "Income of this Month",
                titleTextStyle: {
                    color: 'black',
                    bold: true
                },
                width: 600,
                height: 400,
                backgroundColor: "#e6e6e6"
            };

            var chart = new google.visualization.PieChart(document.getElementById("income-by-group"));

            if (data.getNumberOfRows() > 0) {
                chart.draw(data, options);
            } else {
                console.log('Pie chart data is empty or invalid');
            }
        }
    </script>

    <script type="text/javascript">
        google.charts.load("current", {
            packages: ["line"]
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn("number", "Day");
            data.addColumn("number", "Amount");
            <?php
            foreach ($linegraph_data as $line) {
                echo "data.addRows([[" . addslashes($line['DAY(date)']) . ", " . $line['SUM(income.income_amount)'] . "]]);\n";
            }
            ?>
            var options = {
                chart: {
                    title: "Income By Day",
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
                document.getElementById("income-by-day")
            );

            chart.draw(data, google.charts.Line.convertOptions(options));
        }
    </script>

    <script type="text/javascript">
        google.charts.load("current", {
            packages: ["corechart"]
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            // Start with a static header row for the chart
            var data = google.visualization.arrayToDataTable([
                ["Category Name", "TotalSpend", {
                    role: "style"
                }]
                <?php
                foreach ($bargraph_data as $bar) {
                    $category_name = addslashes($bar['category_name']);
                    $total_spend = $bar['SUM(income.income_amount)'];
                    $color = 'blue'; // You can customize this based on your data if needed
                    echo ",['$category_name', $total_spend, '$color']"; // This adds a row for each bar
                }
                ?>
            ]);

            var options = {
                title: "Income By Category",
                titleTextStyle: {
                    color: 'black',
                    bold: true
                },
                width: 600,
                height: 400,
                bar: {
                    groupWidth: "95%"
                },
                legend: {
                    position: "none"
                },
                backgroundColor: "#e6e6e6",
            };

            var chart = new google.visualization.BarChart(document.getElementById("top-income-categories"));
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
            <form action="../../App/Controller/IncomeController.php" method="post">
                <h2>Add New Income</h2>
                <input
                    required
                    type="number"
                    name="amount"
                    placeholder="Enter amount" />
                <select name="category-id" id="category-id" required>
                    <option value="" selected>Select the Category</option>
                    <?php
                    foreach ($income_category as $category) {
                        echo '<option value="' . $category['category_id'] . '">' . $category['category_name'] . '</option>';
                    }
                    ?>
                </select>
                <input
                    required
                    type="text"
                    name="remarks"
                    placeholder="Enter remarks" />
                <button type="submit" name="submit" value="add-income">
                    Add income
                </button>
            </form>

            <form action="../../App/Controller/IncomeController.php" method="post">
                <h2>Add Income Category</h2>
                <input
                    type="text"
                    name="category-name"
                    placeholder="Enter New category" />
                <button type="submit" name="submit" value="add-category">
                    Add Category
                </button>
            </form>
        </div>

        <div class="transactions">
            <h2>Transactions</h2>
            <div class="filter-section">
                <form action="../../App/Controller/IncomeController.php" method="post">
                    <label for="startFilterDate">Start Date:</label>
                    <input type="month" name="startFilterDate"
                        value="<?= isset($_POST['startFilterDate']) ? $_POST['startFilterDate'] : '' ?>" />

                    <label for="endFilterDate">End Date:</label>
                    <input type="month" name="endFilterDate"
                        value="<?= isset($_POST['endFilterDate']) ? $_POST['endFilterDate'] : '' ?>" />

                    <button type="submit" name="submit" value="filter">Filter</button>
                </form>
            </div>

        </div>

        <table class="transactions-table">
            <tr>
                <th>Income Amount</th>
                <th>Category</th>
                <th>Remarks</th>
                <th>Date</th>
                <th>Time</th>
                <th>Action</th>
            </tr>
            <tr>
                <?php
                foreach ($income_transaction as $transaction) {
                    echo '<form action="../../App/Controller/IncomeController.php" method="post">';
                    echo "<tr>";

                    echo "<td>" .
                        "<label for='amount" . $transaction['income_id'] . "' "
                        . "id='defaultAmount" . $transaction['income_id'] . "'>"
                        . $transaction['income_amount'] . "</label>" .
                        "<input type='number' name='amount' "
                        . "id='amount-" . $transaction['income_id'] . ""
                        . "' value='" . $transaction['income_amount'] . "' hidden/>" .
                        "</td>";

                    echo "<td>" .
                        "<span id='defaultCategory" . $transaction['income_id'] . "'>"
                        . $transaction['category_name'] . "</span>" .
                        "<select name='categoryId' id='categoryId-" . $transaction['income_id'] . "' hidden>" .
                        "<option selected value='" . $transaction['income_category'] . "'>"
                        . $transaction['category_name'] . "</option>";

                    foreach ($income_category as $category) {
                        echo "<option value='" . $category['category_id'] . "'>"
                            . $category['category_name'] . "</option>";
                    }
                    echo "</select>" .
                        "</td>";

                    echo "<td>" .
                        "<label for='remarks" . $transaction['income_id'] . "' i"
                        . "d='defaultRemarks" . $transaction['income_id'] . "'>"
                        . $transaction['remarks'] . "</label>" .
                        "<input type='text' name='remarks'"
                        . " id='remarks-" . $transaction['income_id'] . "'"
                        . " value='class='entry'" . $transaction['remarks'] . "' hidden/>" .
                        "</td>";

                    echo '<td>' . $transaction['date'] . '</td>';
                    echo '<td>' . $transaction['time'] . '</td>';

                    echo "<td>" .
                        "<input type='number' value='" . $transaction['income_id'] . "' "
                        . "name='income-id'  hidden>" .
                        "<button type='submit' name='submit' "
                        . "id='delete-" . $transaction['income_id'] . "'"
                        . " value='delete'>Delete now</button>" .
                        "<button type='button' "
                        . "id='edit-" . $transaction['income_id'] . "' "
                        . "onclick='edit(" . $transaction['income_id'] . ")'>Edit</button>" .
                        "<button type='submit' name='submit' "
                        . "id='update-" . $transaction['income_id'] . "' "
                        . "value='update' hidden='hidden'>Update Now</button>" .
                        "<button type='button' "
                        . "id='back-" . $transaction['income_id'] . "' "
                        . "hidden='hidden' onclick='back(" . $transaction['income_id'] . ")'>Back</button>" .
                        "</td>";
                    echo "</tr>";
                    echo "</form>";
                }

                ?>

            </tr>
        </table>

        <h2>Charts</h2>
        <div class="charts">
            <div class="chart" id="top-income-categories">
                <!-- Placeholder for Bar Chart -->
            </div>
            <div class="chart" id="income-by-group">
                <!-- Placeholder for Pie Chart -->
            </div>
            <div class="chart" id="income-by-day">
                <!-- Placeholder for Line Chart -->
            </div>
            <div class="chart" id="income-by-week">
                <!-- Placeholder for Line Chart -->
            </div>
        </div>

    </div>
</body>

</html>