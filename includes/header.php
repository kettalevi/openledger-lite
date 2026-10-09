<?php
require_once __DIR__ . '/auth.php';
$current = $current ?? '';
$pageTitle = $pageTitle ?? 'OpenLedger Lite';
$nav = [
    'dashboard' => ['Dashboard', 'dashboard/index.php'],
    'income'    => ['Income', 'income/index.php'],
    'expenses'  => ['Expenses', 'expenses/index.php'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> · OpenLedger Lite</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="md:flex min-h-screen">

    <!-- Sidebar -->
    <aside class="md:w-64 bg-blue-900 text-white p-5 md:min-h-screen">
        <h1 class="text-2xl font-bold mb-6">OpenLedger</h1>
        <ul class="space-y-2">
            <?php foreach ($nav as $key => [$label, $path]): ?>
                <li><a href="<?= e(url($path)) ?>"
                       class="block p-2 rounded hover:bg-blue-700 <?= $current === $key ? 'bg-blue-700 font-semibold' : '' ?>"><?= e($label) ?></a></li>
            <?php endforeach; ?>
            <li>
                <form method="POST" action="<?= e(url('auth/logout.php')) ?>">
                    <?= csrf_field() ?>
                    <button class="block w-full text-left p-2 rounded hover:bg-red-600">Logout</button>
                </form>
            </li>
        </ul>
    </aside>

    <main class="flex-1 p-4 md:p-6 min-w-0">
        <?php foreach (flashes() as $f): ?>
            <div class="mb-4 p-3 rounded <?= $f['type'] === 'error' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' ?>">
                <?= e($f['msg']) ?>
            </div>
        <?php endforeach; ?>
