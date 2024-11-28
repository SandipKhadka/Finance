<?php
require_once __DIR__ . '/../../App/Controller/DashboardController.php';
$dashboard_controller = new DashboardController();

$income = 0;

if (isset($_SESSION['income'])) {
    $income = $_SESSION['income'];
    unset($_SESSION['income']);
}

if (isset($_SESSION['expenses'])) {
    $expenses = $_SESSION['expenses'];
    unset($_SESSION['expenses']);
}

$piechart_data = [];
if (isset($_SESSION['piechart_data'])) {
    $piechart_data = $_SESSION['piechart_data'];
    unset($_SESSION['piechart_data']);
}


$linegraph_data = [];
if (isset($_SESSION['linegraph_data'])) {
    $linegraph_data = $_SESSION['linegraph_data'];
    unset($_SESSION['linegraph_data']);
}

$bargraph_data = [];
if (isset($_SESSION['bargraph_data'])) {
    $bargraph_data = $_SESSION['bargraph_data'];
    unset($_SESSION['bargraph_data']);
}

$net_income = $income - $expenses;
$dashboard_controller->close_db_connection();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Expense Trend Dashboard</title>
    <link rel="stylesheet" href="../CSS/dashboard.css"/>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


    <script type="text/javascript">
        google.charts.load("current", {packages: ["corechart"]});

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
                title: "Expenses Of This Month",
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
        google.charts.load("current", {packages: ["line"]});
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
                    subtitle: "in millions of dollars (USD)",
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
        google.charts.load("current", {packages: ["corechart"]});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            // Start with a static header row for the chart
            var data = google.visualization.arrayToDataTable([
                ["Category Name", "TotalSpend", {role: "style"}]
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
                titleTextStyle: {color: 'black', bold: true},
                width: 600,
                height: 400,
                bar: {groupWidth: "95%"},
                legend: {position: "none"},
                backgroundColor: "#e6e6e6",
            };

            var chart = new google.visualization.BarChart(document.getElementById("top-expenses-categories"));
            chart.draw(data, options);
        }
    </script>

</head>

<body>
<div class="container">
    <button>
        <a href="../../App/Controller/UserController.php?submit=logout" id="logout">Log out</a>
    </button>
    <button>
    <a href="change_password.php">Change Password</a>
    </button>
    <h1>BudgetBuddy</h1>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-title">Income</div>
            <div class="summary-value">Rs . <?php echo $income ?></div>
        </div>
        <div class="summary-item">
            <div class="summary-title">Expenses</div>
            <div class="summary-value">Rs . <?php echo $expenses ?></div>
        </div>
        <div class="summary-item">
            <div class="summary-title">Net Income</div>
            <div class="summary-value">Rs . <?php echo $net_income ?></div>

        </div>
    </div>
    
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

    <div class="charts">
        <div class="chart" id="expenses-by-group">
            <!-- Placeholder for Pie Chart -->
        </div>
        <div class="chart" class="bottomcharts" id="top-expenses-categories">
            <!-- Placeholder for Bar Chart -->
        </div>
        <div class="chart" id="expenses-by-week">
            <!-- Placeholder for Line Chart -->
        </div>
        <div class="chart" class="bottomcharts" id="expenses-by-day">
            <!-- Placeholder for Bar Chart -->
        </div>
    </div>
</div>

</body>

</html>