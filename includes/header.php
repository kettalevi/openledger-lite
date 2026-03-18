<?php require_once __DIR__ . '/auth.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>OpenLedger Lite</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="flex">

    <!-- Sidebar -->
    <div class="w-64 bg-blue-900 text-white min-h-screen p-5">
        <h1 class="text-2xl font-bold mb-6">OpenLedger</h1>

        <ul class="space-y-3">
            <li><a href="/dashboard/index.php" class="block hover:bg-blue-700 p-2 rounded">Dashboard</a></li>
<li><a href="/income/index.php" class="block hover:bg-blue-700 p-2 rounded">Income</a></li>
<li><a href="/expenses/index.php" class="block hover:bg-blue-700 p-2 rounded">Expenses</a></li>
            <li><a href="/auth/logout.php" class="block hover:bg-red-600 p-2 rounded">Logout</a></li>
        </ul>
    </div>

    <!-- Content -->
    <div class="flex-1 p-6">
