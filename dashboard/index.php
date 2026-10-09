<?php
require_once '../includes/db.php';
$current = 'dashboard';
$pageTitle = 'Dashboard';
require_once '../includes/header.php';

$totalIncome = (float)$conn->query('SELECT COALESCE(SUM(amount),0) FROM income')->fetchColumn();
$totalExpenses = (float)$conn->query('SELECT COALESCE(SUM(amount),0) FROM expenses')->fetchColumn();
$balance = $totalIncome - $totalExpenses;

// Last 12 months, including months with no activity.
$months = [];
for ($i = 11; $i >= 0; $i--) {
    $months[date('Y-m', strtotime("first day of -$i month"))] = ['income' => 0.0, 'expenses' => 0.0];
}
$start = array_key_first($months) . '-01';

foreach (['income' => 'income_date', 'expenses' => 'expense_date'] as $table => $col) {
    $stmt = $conn->prepare("SELECT DATE_FORMAT($col, '%Y-%m') AS m, SUM(amount) AS t FROM $table WHERE $col >= ? GROUP BY m");
    $stmt->execute([$start]);
    foreach ($stmt as $r) {
        if (isset($months[$r['m']])) {
            $months[$r['m']][$table] = (float)$r['t'];
        }
    }
}
$thisMonth = $months[date('Y-m')];

// Expense breakdown (all time; top categories).
$categoryData = $conn->query("
    SELECT COALESCE(c.name, 'Uncategorized') AS name, SUM(e.amount) AS total
    FROM expenses e LEFT JOIN expense_categories c ON e.category_id = c.id
    GROUP BY c.id, c.name ORDER BY total DESC
")->fetchAll();

// Recent activity.
$recent = $conn->query("
    (SELECT 'income' AS type, i.income_date AS d, i.id, i.amount, COALESCE(s.name,'') AS label, i.contributor_name AS note
       FROM income i LEFT JOIN income_sources s ON s.id = i.source_id)
    UNION ALL
    (SELECT 'expense', e.expense_date, e.id, e.amount, COALESCE(c.name,''), e.description
       FROM expenses e LEFT JOIN expense_categories c ON c.id = e.category_id)
    ORDER BY d DESC, id DESC LIMIT 8
")->fetchAll();
?>

<h2 class="text-2xl font-bold mb-6">Dashboard</h2>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-5 rounded shadow">
        <h3 class="text-gray-500">Total Income</h3>
        <p class="text-2xl font-bold text-green-600"><?= e(money($totalIncome)) ?></p>
        <p class="text-xs text-gray-500 mt-1">This month: <?= e(money($thisMonth['income'])) ?></p>
    </div>
    <div class="bg-white p-5 rounded shadow">
        <h3 class="text-gray-500">Total Expenses</h3>
        <p class="text-2xl font-bold text-red-600"><?= e(money($totalExpenses)) ?></p>
        <p class="text-xs text-gray-500 mt-1">This month: <?= e(money($thisMonth['expenses'])) ?></p>
    </div>
    <div class="bg-white p-5 rounded shadow">
        <h3 class="text-gray-500">Balance</h3>
        <p class="text-2xl font-bold <?= $balance < 0 ? 'text-red-600' : 'text-blue-600' ?>"><?= e(money($balance)) ?></p>
        <p class="text-xs text-gray-500 mt-1">This month: <?= e(money($thisMonth['income'] - $thisMonth['expenses'])) ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white p-5 rounded shadow">
        <h3 class="mb-4 font-bold">Monthly Income vs Expenses (last 12 months)</h3>
        <canvas id="financeChart"></canvas>
    </div>
    <div class="bg-white p-5 rounded shadow">
        <h3 class="mb-4 font-bold">Expenses by Category</h3>
        <?php if ($categoryData): ?>
            <canvas id="categoryChart"></canvas>
        <?php else: ?>
            <p class="text-gray-500">No expenses recorded yet.</p>
        <?php endif; ?>
    </div>
</div>

<div class="bg-white p-5 rounded shadow overflow-x-auto">
    <h3 class="mb-4 font-bold">Recent Activity</h3>
    <table class="w-full table-auto">
        <tbody>
        <?php foreach ($recent as $r): ?>
            <tr class="border-b">
                <td class="p-2 whitespace-nowrap"><?= e($r['d']) ?></td>
                <td class="p-2"><span class="text-xs px-2 py-1 rounded <?= $r['type'] === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>"><?= e(ucfirst($r['type'])) ?></span></td>
                <td class="p-2"><?= e($r['label']) ?></td>
                <td class="p-2 text-gray-500"><?= e($r['note']) ?></td>
                <td class="p-2 text-right font-bold whitespace-nowrap <?= $r['type'] === 'income' ? 'text-green-600' : 'text-red-600' ?>">
                    <?= $r['type'] === 'income' ? '+' : '−' ?><?= e(money($r['amount'])) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$recent): ?>
            <tr><td class="p-4 text-center text-gray-500">Nothing yet — add your first income or expense.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const months = <?= json_encode($months) ?>;
const labels = Object.keys(months);

new Chart(document.getElementById('financeChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            { label: 'Income',   data: labels.map(m => months[m].income),   borderColor: '#16a34a', backgroundColor: '#16a34a', borderWidth: 2, tension: 0.25 },
            { label: 'Expenses', data: labels.map(m => months[m].expenses), borderColor: '#dc2626', backgroundColor: '#dc2626', borderWidth: 2, tension: 0.25 }
        ]
    },
    options: { scales: { y: { beginAtZero: true } } }
});

const categoryData = <?= json_encode($categoryData) ?>;
const catCanvas = document.getElementById('categoryChart');
if (catCanvas) {
    const palette = ['#2563eb','#dc2626','#16a34a','#d97706','#7c3aed','#0891b2','#db2777','#65a30d','#475569','#ea580c'];
    new Chart(catCanvas, {
        type: 'pie',
        data: {
            labels: categoryData.map(c => c.name),
            datasets: [{ data: categoryData.map(c => +c.total), backgroundColor: categoryData.map((_, i) => palette[i % palette.length]) }]
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
