<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

// Fetch sources
$sources = $conn->query("SELECT * FROM income_sources ORDER BY name ASC")->fetchAll();

// Filters
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$source_id = $_GET['source_id'] ?? '';
$search = $_GET['search'] ?? '';

// Build WHERE
$where = [];
$params = [];

if ($from && $to) {
    $where[] = "i.income_date BETWEEN ? AND ?";
    $params[] = $from;
    $params[] = $to;
}

if ($source_id) {
    $where[] = "i.source_id = ?";
    $params[] = $source_id;
}

if ($search) {
    $where[] = "(i.contributor_name LIKE ? OR s.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

// Fetch data
$stmt = $conn->prepare("
    SELECT i.*, s.name AS source_name
    FROM income i
    LEFT JOIN income_sources s ON i.source_id = s.id
    $whereSQL
    ORDER BY i.income_date DESC
");
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Summary
$totalStmt = $conn->prepare("
    SELECT SUM(i.amount)
    FROM income i
    LEFT JOIN income_sources s ON i.source_id = s.id
    $whereSQL
");
$totalStmt->execute($params);
$totalIncome = $totalStmt->fetchColumn() ?? 0;

$count = count($rows);
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Income Records</h2>

    <div class="flex gap-2">
        <a href="../exports/income_csv.php?from=<?= $from ?>&to=<?= $to ?>" 
           class="bg-green-600 text-white px-4 py-2 rounded">Export CSV</a>

        <a href="create.php" 
           class="bg-blue-600 text-white px-4 py-2 rounded">+ Add Income</a>
    </div>
</div>

<!-- FILTER -->
<form method="GET" class="bg-white p-4 rounded shadow mb-6 grid grid-cols-5 gap-4 items-end">

    <input type="date" name="from" value="<?= $from ?>" class="border p-2 rounded">
    <input type="date" name="to" value="<?= $to ?>" class="border p-2 rounded">

    <select name="source_id" class="border p-2 rounded">
        <option value="">All Sources</option>
        <?php foreach ($sources as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $source_id == $s['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['name']) ?>
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
