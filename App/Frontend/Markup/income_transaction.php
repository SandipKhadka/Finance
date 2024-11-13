<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="css/income_transaction.css">
    <title>Income Transaction</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load("current", {packages: ["corechart"]});

        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn("string", "Category");
            data.addColumn("number", "Amount");
            <c:forEach items="${pieChartData}" var="item">
                data.addRow(['${item.name}', ${item.amount}]);
            </c:forEach>

            var options = {
                title: "Income of this month",
                titleTextStyle: {
                    color: 'white',
                    bold: false
                },
                width: 600,
                height: 400,
                backgroundColor: "#333"
            };
            var chart = new google.visualization.PieChart(
                document.getElementById("income-by-group")
            );
            chart.draw(data, options);
        }
    </script>
    <script type="text/javascript">
        google.charts.load("current", {packages: ["line"]});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn("number", "Day");
            data.addColumn("number", "Amount");
            <c:forEach var="income" items="${incomeByDay}">
                data.addRows([[${income.day}, ${income.amount}]]);
            </c:forEach>
            var options = {
                chart: {
                    title: "Income by day",
                    subtitle: "in millions of dollars (USD)",
                },
                titleTextStyle: {
                    color: 'white',
                    bold: false
                },
                width: 600,
                height: 400,
                backgroundColor: "#333"
            };

            var chart = new google.charts.Line(
                document.getElementById("income-by-day")
            );

            chart.draw(data, google.charts.Line.convertOptions(options));
        }
    </script>
    <script type="text/javascript">
        google.charts.load("current", {packages: ["corechart"]});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn("string", "Category");
            data.addColumn("number", "Amount");
            data.addColumn({role: "style"});
            <c:forEach var="income" items="${topFiveCategory}">
                data.addRow(['${income.name}', ${income.amount}, 'blue']);
            </c:forEach>

            var view = new google.visualization.DataView(data);
            view.setColumns([
                0,
                1,
                {
                    calc: "stringify",
                    sourceColumn: 1,
                    type: "string",
                    role: "annotation",
                },
                2,
            ]);

            var options = {
                title: "Income top five category",
                titleTextStyle: {
                    color: 'white',
                    bold: false
                },
                width: 600,
                height: 400,
                bar: {groupWidth: "95%"},
                legend: {position: "none"},
                backgroundColor: "#333",
            };
            var chart = new google.visualization.BarChart(
                document.getElementById("top-income-categories")
            );
            chart.draw(view, options);
        }
    </script>
</head>
<body>
<div class="container">
    <a href="dashboard">
        <button>&#x2190; Dashboard</button>
    </a>
    <div class="income-form">
        <form action="../../Backend/Controller/IncomeController.php" method="post">
            <h2>Add Income</h2>
            <input
                required
                type="number"
                name="amount"
                placeholder="Enter amount"
            />
            <select name="category-id" id="category-id">
                <option value="" selected>Select the Category</option>
            </select>
            <input
                required
                type="text"
                name="remarks"
                placeholder="Enter remarks"
            />
            <button type="submit" name="submit" value="add-income">
                Add income
            </button>
        </form>

        <form action="income" method="post">
            <h2>Add New Category</h2>
            <input
                type="text"
                name="categoryName"
                placeholder="Enter New category"
            />
            <button type="submit" name="submit" value="addCategory">
                Add Category
            </button>
        </form>
    </div>

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
    <div class="transactions">
        <h2>Transactions</h2>
        <div class="filter-section">
            <form action="income" method="get">
                <input type="month" name="startFilterDate"/>
                <input type="month" name="endFilterDate">
                <button type="submit">Filter</button>
            </form>
        </div>
    </div>

    <table class="transactions-table">
        <tr>
            <th>Expenses Amount</th>
            <th>Category</th>
            <th>Remarks</th>
            <td>Date</td>
            <td>Time</td>
            <th>Action</th>
        </tr>
        <c:forEach var="income" items="${transactions}">
            <tr>
                <form action="income" method="post">
                    <td>
                        <label for="amount-${income.transactionId}"
                               id="defaultAmount-${income.transactionId}">${income.amount}</label>
                        <input type="number" name="amount" id="amount-${income.transactionId}"
                               value="${income.amount}" hidden="hidden"></td>
                    <td>
                        <span id="defaultCategory-${income.transactionId}">${income.category}</span>
                        <select name="categoryId" id="categoryId-${income.transactionId}" required hidden="hidden">
                            <option selected value="${income.categoryId}">${income.category}</option>
                            <c:forEach var="category" items="${categoryNames}">
                                <option value="${category.categoryId}">
                                    ${category.categoryName}
                                </option>
                                `
                            </c:forEach>
                        </select>

                    </td>
                    <td>
                        <label for="remarks-${income.transactionId}"
                               id="defaultRemarks-${income.transactionId}">${income.remarks}</label>
                        <input type="text" name="remarks" hidden="hidden" id="remarks-${income.transactionId}"
                               value="${income.remarks}"></td>
                    <td>${income.date}</td>
                    <td>${income.time}</td>
                    <td>
                        <input type="number" value="${income.transactionId}" hidden="hidden" name="incomeId"
                               id="id-${income.transactionId}">
                        <button type="submit" name="submit" id="delete-${income.transactionId}" value="delete">Delete
                            now
                        </button>
                        <button type="button" id="edit-${income.transactionId}"
                                onclick="edit(${income.transactionId})">Edit
                        </button>
                        <button type="submit" name="submit" id="update-${income.transactionId}" value="update"
                                hidden="hidden">Update Now
                        </button>

                        <button type="button" id="back-${income.transactionId}" hidden="hidden"
                                onclick="back(${income.transactionId})">Back
                        </button>
                    </td>
                </form>
            </tr>
        </c:forEach>
    </table>

</div>
</body>
</html>
<script src="js/editButton.js">
</script>