<?php
require_once '../includes/db.php';
$current = 'expenses';
$pageTitle = 'Expenses';
require_once '../includes/header.php';

$categories = $conn->query('SELECT id, name FROM expense_categories ORDER BY name')->fetchAll();

$f = list_filters('e.expense_date', 'e.category_id', 'category_id', ['e.description', 'c.name']);
$from = $f['from']; $to = $f['to']; $category_id = $f['id']; $search = $f['search'];
$from_sql = "FROM expenses e LEFT JOIN expense_categories c ON e.category_id = c.id {$f['sql']}";

$stmt = $conn->prepare("SELECT COUNT(*), COALESCE(SUM(e.amount), 0) $from_sql");
$stmt->execute($f['params']);
[$count, $totalExpenses] = $stmt->fetch(PDO::FETCH_NUM);

$pages = max(1, (int)ceil($count / PER_PAGE));
$page = min(max(1, (int)($_GET['page'] ?? 1)), $pages);
$offset = ($page - 1) * PER_PAGE;

$stmt = $conn->prepare("
    SELECT e.id, e.expense_date, e.amount, e.description, c.name AS category_name
    $from_sql
    ORDER BY e.expense_date DESC, e.id DESC
    LIMIT " . PER_PAGE . " OFFSET $offset
");
$stmt->execute($f['params']);
$rows = $stmt->fetchAll();
?>

<div class="flex flex-wrap justify-between items-center gap-2 mb-6">
    <h2 class="text-2xl font-bold">Expense Records</h2>
    <div class="flex gap-2">
        <a href="<?= e(url('exports/expenses_csv.php') . qs(['page' => null])) ?>"
           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Export CSV</a>
        <a href="create.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Expense</a>
    </div>
</div>

<form method="GET" class="bg-white p-4 rounded shadow mb-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
    <input type="date" name="from" value="<?= e($from) ?>" title="From" class="border p-2 rounded">
    <input type="date" name="to" value="<?= e($to) ?>" title="To" class="border p-2 rounded">
    <select name="category_id" class="border p-2 rounded">
        <option value="">All Categories</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= $category_id == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search..." class="border p-2 rounded">
    <div class="flex gap-2">
        <button class="bg-gray-800 text-white px-4 py-2 rounded flex-1">Filter</button>
        <a href="index.php" class="px-4 py-2 border rounded text-center">Reset</a>
    </div>
</form>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
    <div class="bg-white p-4 rounded shadow">
        <h3 class="text-gray-500">Total Expenses</h3>
        <p class="text-xl font-bold text-red-600"><?= e(money($totalExpenses)) ?></p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <h3 class="text-gray-500">Entries</h3>
        <p class="text-xl font-bold"><?= (int)$count ?></p>
    </div>
</div>

<div class="bg-white p-4 rounded shadow overflow-x-auto">
<table class="w-full table-auto">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 text-left">Date</th>
            <th class="p-2 text-left">Category</th>
            <th class="p-2 text-left">Description</th>
            <th class="p-2 text-right">Amount</th>
            <th class="p-2 text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): ?>
        <tr class="border-b">
            <td class="p-2 whitespace-nowrap"><?= e($r['expense_date']) ?></td>
            <td class="p-2"><?= e($r['category_name']) ?></td>
            <td class="p-2"><?= e($r['description']) ?></td>
            <td class="p-2 text-right text-red-600 font-bold whitespace-nowrap"><?= e(money($r['amount'])) ?></td>
            <td class="p-2 text-right whitespace-nowrap">
                <a href="edit.php?id=<?= (int)$r['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                <form method="POST" action="delete.php" class="inline"
                      onsubmit="return confirm('Delete this expense?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <button class="text-red-600 hover:underline ml-2">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
        <tr><td colspan="5" class="p-6 text-center text-gray-500">No expenses found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

<?php require '../includes/pagination.php'; ?>
<?php require_once '../includes/footer.php'; ?>
