<?php
require_once '../includes/db.php';
require_once '../includes/header.php';
?>

<h2 class="text-2xl font-bold mb-6">Add Expense</h2>

<form action="store.php" method="POST" class="bg-white p-6 rounded shadow max-w-xl">

    <div class="mb-4">
        <label class="block text-gray-600">Amount</label>
        <input type="number" step="0.01" name="amount" required
               class="w-full p-2 border rounded">
    </div>

    <div class="mb-4">
        <label class="block text-gray-600">Category</label>
        <input type="text" name="category_name" placeholder="e.g Rent, Fuel, Utilities"
               required class="w-full p-2 border rounded">
    </div>

    <div class="mb-4">
        <label class="block text-gray-600">Description</label>
        <textarea name="description" rows="3"
                  class="w-full p-2 border rounded"></textarea>
    </div>

    <div class="mb-4">
        <label class="block text-gray-600">Date</label>
        <input type="date" name="expense_date" required
               class="w-full p-2 border rounded">
    </div>

    <button class="bg-red-600 text-white px-4 py-2 rounded">
        Save Expense
    </button>
</form>

</div></div></body></html>
