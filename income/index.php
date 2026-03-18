<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

// Filters
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = "";
$params = [];

if ($from && $to) {
    $where = "WHERE income_date BETWEEN ? AND ?";
    $params = [$from, $to];
}

// Fetch Data
$stmt = $conn->prepare("
    SELECT i.*, s.name AS source_name
    FROM income i
    LEFT JOIN income_sources s ON i.source_id = s.id
    $where
    ORDER BY i.income_date DESC
");
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Summary
$total = $conn->prepare("
    SELECT SUM(amount) FROM income $where
");
$total->execute($params);
$totalIncome = $total->fetchColumn() ?? 0;

$count = count($rows);
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Income Records</h2>
    <a href="create.php" class="bg-blue-600 text-white px-4 py-2 rounded">
        + Add Income
    </a>
</div>

<!-- FILTER -->
<form method="GET" class="bg-white p-4 rounded shadow mb-6 flex gap-4 items-end">
    <div>
        <label class="text-sm">From</label>
        <input type="date" name="from" value="<?= $from ?>" class="border p-2 rounded">
    </div>

    <div>
        <label class="text-sm">To</label>
        <input type="date" name="to" value="<?= $to ?>" class="border p-2 rounded">
    </div>

    <button class="bg-gray-800 text-white px-4 py-2 rounded">Filter</button>
</form>

<!-- SUMMARY CARDS -->
<div class="grid grid-cols-2 gap-6 mb-6">
    <div class="bg-white p-4 rounded shadow">
        <h3 class="text-gray-500">Total Income</h3>
        <p class="text-xl font-bold text-green-600">
            UGX <?= number_format($totalIncome) ?>
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
            <th class="p-2 text-left">Source</th>
            <th class="p-2 text-left">Contributor</th>
            <th class="p-2 text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): ?>
        <tr class="border-b">
            <td class="p-2"><?= $r['income_date'] ?></td>
            <td class="p-2"><?= htmlspecialchars($r['source_name']) ?></td>
            <td class="p-2"><?= htmlspecialchars($r['contributor_name']) ?></td>
            <td class="p-2 text-right text-green-600 font-bold">
                UGX <?= number_format($r['amount']) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>

</div></div></body></html>
