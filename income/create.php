<?php
require_once '../includes/db.php';
require_once '../includes/header.php';
?>

<h2 class="text-2xl font-bold mb-6">Add Income</h2>

<form action="store.php" method="POST" class="bg-white p-6 rounded shadow max-w-xl">

    <div class="mb-4">
        <label class="block text-gray-600">Amount</label>
        <input type="number" step="0.01" name="amount" required
               class="w-full p-2 border rounded">
    </div>

    <div class="mb-4">
        <label class="block text-gray-600">Source</label>
        <input type="text" name="source_name" placeholder="e.g Donations, Sales"
               required class="w-full p-2 border rounded">
    </div>

    <div class="mb-4">
        <label class="block text-gray-600">Contributor Name</label>
        <input type="text" name="contributor_name"
               class="w-full p-2 border rounded">
    </div>

    <div class="mb-4">
        <label class="block text-gray-600">Date</label>
        <input type="date" name="income_date" required
               class="w-full p-2 border rounded">
    </div>

    <button class="bg-green-600 text-white px-4 py-2 rounded">
        Save Income
    </button>
</form>

</div></div></body></html>
