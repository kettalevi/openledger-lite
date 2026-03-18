<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

// Totals
$totalIncome = $conn->query("SELECT SUM(amount) FROM income")->fetchColumn() ?? 0;
$totalExpenses = $conn->query("SELECT SUM(amount) FROM expenses")->fetchColumn() ?? 0;
$balance = $totalIncome - $totalExpenses;

// Monthly Data (Last 6 months)
$monthlyData = $conn->query("
    SELECT 
        DATE_FORMAT(income_date, '%Y-%m') as month,
        SUM(amount) as total
    FROM income
    GROUP BY month
")->fetchAll(PDO::FETCH_KEY_PAIR);

$expenseMonthly = $conn->query("
    SELECT 
        DATE_FORMAT(expense_date, '%Y-%m') as month,
        SUM(amount) as total
    FROM expenses
    GROUP BY month
")->fetchAll(PDO::FETCH_KEY_PAIR);

// Expense Categories Breakdown
$categoryData = $conn->query("
    SELECT c.name, SUM(e.amount) as total
    FROM expenses e
    LEFT JOIN expense_categories c ON e.category_id = c.id
    GROUP BY c.name
")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="text-2xl font-bold mb-6">Dashboard</h2>

<!-- SUMMARY CARDS -->
<div class="grid grid-cols-3 gap-6 mb-8">

    <div class="bg-white p-5 rounded shadow">
        <h3 class="text-gray-500">Total Income</h3>
        <p class="text-2xl font-bold text-green-600">
            UGX <?= number_format($totalIncome) ?>
        </p>
    </div>

    <div class="bg-white p-5 rounded shadow">
        <h3 class="text-gray-500">Total Expenses</h3>
        <p class="text-2xl font-bold text-red-600">
            UGX <?= number_format($totalExpenses) ?>
        </p>
    </div>

    <div class="bg-white p-5 rounded shadow">
        <h3 class="text-gray-500">Balance</h3>
        <p class="text-2xl font-bold text-blue-600">
            UGX <?= number_format($balance) ?>
        </p>
    </div>

</div>

<!-- CHARTS -->
<div class="grid grid-cols-2 gap-6">

    <!-- Income vs Expense -->
    <div class="bg-white p-5 rounded shadow">
        <h3 class="mb-4 font-bold">Monthly Income vs Expenses</h3>
        <canvas id="financeChart"></canvas>
    </div>

    <!-- Category Breakdown -->
    <div class="bg-white p-5 rounded shadow">
        <h3 class="mb-4 font-bold">Expense by Category</h3>
        <canvas id="categoryChart"></canvas>
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const incomeData = <?= json_encode($monthlyData) ?>;
const expenseData = <?= json_encode($expenseMonthly) ?>;

const labels = Object.keys(incomeData);

const incomeValues = Object.values(incomeData);
const expenseValues = labels.map(m => expenseData[m] ?? 0);

// Line Chart
new Chart(document.getElementById('financeChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Income',
                data: incomeValues,
                borderWidth: 2
            },
            {
                label: 'Expenses',
                data: expenseValues,
                borderWidth: 2
            }
        ]
    }
});

// Pie Chart
const categoryData = <?= json_encode($categoryData) ?>;

new Chart(document.getElementById('categoryChart'), {
    type: 'pie',
    data: {
        labels: categoryData.map(c => c.name),
        datasets: [{
            data: categoryData.map(c => c.total)
        }]
    }
});
</script>

</div></div></body></html>
