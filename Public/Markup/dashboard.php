<?php
require_once __DIR__ . '/../../App/Controller/DashboardController.php';
$dashboard_controller = new DashboardController();
session_start();

$income =0;

if(isset($_SESSION['income'])) {
    $income = $_SESSION['income'];
    unset($_SESSION['income']);
}
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
        google.charts.load('current', {'packages': ['corechart']});

        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Category');
            data.addColumn('number', 'Amount');
            <c:forEach items="${pieChartData}" var="item">
                data.addRow(['${item.name}', ${item.amount}]);
            </c:forEach>

            var options = {
                'title': 'Expenses of this month',
                'width': 400,
                'height': 300
            };
            var chart = new google.visualization.PieChart(document.getElementById('expenses-by-group'));
            chart.draw(data, options);
        }
    </script>
    <script type="text/javascript">
        google.charts.load('current', {'packages': ['line']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            var data = new google.visualization.DataTable();
            data.addColumn('number', 'Day');
            data.addColumn('number', 'Amount');
            <c:forEach var="expenses" items="${expensesByDay}">
                data.addRows([
                [${expenses.day}, ${expenses.amount}]
                ]);
            </c:forEach>
            var options = {
                chart: {
                    title: 'Expenses by day',
                    subtitle: 'in millions of dollars (USD)'
                },
                width: 400,
                height: 300
            };

            var chart = new google.charts.Line(document.getElementById('expenses-by-day'));

            chart.draw(data, google.charts.Line.convertOptions(options));
        }
    </script>
    <script type="text/javascript">
        google.charts.load("current", {packages: ["corechart"]});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Category');
            data.addColumn('number', 'Amount');
            data.addColumn({role: "style"});
            <c:forEach var="expenses" items="${topFiveCategory}">
                data.addRow(['${expenses.name}', ${expenses.amount}, 'blue']);
            </c:forEach>

            var view = new google.visualization.DataView(data);
            view.setColumns([0, 1,
                {
                    calc: "stringify",
                    sourceColumn: 1,
                    type: "string",
                    role: "annotation"
                },
                2]);

            var options = {
                title: "Expenses top five category",
                width: 600,
                height: 400,
                bar: {groupWidth: "95%"},
                legend: {position: "none"},
            };
            var chart = new google.visualization.BarChart(document.getElementById("top-expense-categories"));
            chart.draw(view, options);
        }
    </script>
</head>

<body>
<div class="container">
    <h1>Dashboard</h1>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-title">Income</div>
            <div class="summary-value">Rs . <?php echo $income ?></div>
        </div>
        <div class="summary-item">
            <div class="summary-title">Expenses</div>
            <div class="summary-value">Rs. ${expenses}</div>
        </div>
        <div class="summary-item">
            <div class="summary-title">Net Income</div>
            <div class="summary-value">Rs. ${netIncome}</div>
        </div>
    </div>

    <a href="income_transaction.php">
        <button>Income Transaction</button>
    </a>
    <a href="ansaction.php">
        <button>Expenses Transaction</button>
    </a>
    <a href="spendlimit">
        <button>Spend Limit</button>
    </a>

    <div class="charts">
        <div class="chart" id="expenses-by-group">
            <!-- Placeholder for Pie Chart -->
        </div>
        <div class="chart" id="top-expense-categories">
            <!-- Placeholder for Bar Chart -->
        </div>
        <div class="chart" id="expenses-by-week">
            <!-- Placeholder for Line Chart -->
        </div>
        <div class="chart" id="expenses-by-day">
            <!-- Placeholder for Bar Chart -->
        </div>
    </div>
</div>

</body>

</html>