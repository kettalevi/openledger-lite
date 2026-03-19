<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

// Fetch categories
$categories = $conn->query("SELECT * FROM expense_categories ORDER BY name ASC")->fetchAll();

// Filters
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$category_id = $_GET['category_id'] ?? '';
$search = $_GET['search'] ?? '';

// WHERE
$where = [];
$params = [];

if ($from && $to) {
    $where[] = "e.expense_date BETWEEN ? AND ?";
    $params[] = $from;
    $params[] = $to;
}

if ($category_id) {
    $where[] = "e.category_id = ?";
    $params[] = $category_id;
}

if ($search) {
    $where[] = "(e.description LIKE ? OR c.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

// Data
$stmt = $conn->prepare("
    SELECT e.*, c.name AS category_name
    FROM expenses e
    LEFT JOIN expense_categories c ON e.category_id = c.id
    $whereSQL
    ORDER BY e.expense_date DESC
");
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Summary
$totalStmt = $conn->prepare("
    SELECT SUM(e.amount)
    FROM expenses e
    LEFT JOIN expense_categories c ON e.category_id = c.id
    $whereSQL
");
$totalStmt->execute($params);
$totalExpenses = $totalStmt->fetchColumn() ?? 0;

$count = count($rows);
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Expense Records</h2>

    <div class="flex gap-2">
        <a href="../exports/expenses_csv.php?from=<?= $from ?>&to=<?= $to ?>" 
           class="bg-green-600 text-white px-4 py-2 rounded">Export CSV</a>

        <a href="create.php" 
           class="bg-blue-600 text-white px-4 py-2 rounded">+ Add Expense</a>
    </div>
</div>

<!-- FILTER -->
<form method="GET" class="bg-white p-4 rounded shadow mb-6 grid grid-cols-5 gap-4 items-end">

    <input type="date" name="from" value="<?= $from ?>" class="border p-2 rounded">
    <input type="date" name="to" value="<?= $to ?>" class="border p-2 rounded">

    <select name="category_id" class="border p-2 rounded">
        <option value="">All Categories</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $category_id == $c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
           placeholder="Search..." class="border p-2 rounded">

    <button class="bg-gray-800 text-white px-4 py-2 rounded">Filter</button>

</form>

<!-- SUMMARY -->
<div class="grid grid-cols-2 gap-6 mb-6">
    <div class="bg-white p-4 rounded shadow">
        <h3 class="text-gray-500">Total Expenses</h3>
        <p class="text-xl font-bold text-red-600">
            UGX <?= number_format($totalExpenses) ?>
        </p>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <h3 class="text-gray-500">Entries</h3>
        <p class="text-xl font-bold"><?= $count ?></p>
    </div>
</div>

<!-- TABLE -->
<div class="bg-white p-4 rounded shadow">
<table class="w-full table-auto">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 text-left">Date</th>
            <th class="p-2 text-left">Category</th>
            <th class="p-2 text-left">Description</th>
            <th class="p-2 text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): ?>
        <tr class="border-b">
            <td class="p-2"><?= $r['expense_date'] ?></td>
            <td class="p-2"><?= htmlspecialchars($r['category_name']) ?></td>
            <td class="p-2"><?= htmlspecialchars($r['description']) ?></td>
            <td class="p-2 text-right text-red-600 font-bold">
                UGX <?= number_format($r['amount']) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

</div></div></body></html>
