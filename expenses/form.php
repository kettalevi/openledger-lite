<?php
/** Shared add/edit form. Expects $entry, $errors, $formTitle, $submitLabel, $categoryNames. */
?>
<h2 class="text-2xl font-bold mb-6"><?= e($formTitle) ?></h2>

<?php foreach ($errors as $err): ?>
    <div class="mb-4 p-3 rounded bg-red-100 text-red-800 max-w-xl"><?= e($err) ?></div>
<?php endforeach; ?>

<form method="POST" class="bg-white p-6 rounded shadow max-w-xl">
    <?= csrf_field() ?>

    <div class="mb-4">
        <label class="block text-gray-600">Amount (<?= e(CURRENCY) ?>)</label>
        <input type="number" step="0.01" min="0.01" name="amount" required
               value="<?= e($entry['amount']) ?>" class="w-full p-2 border rounded">
    </div>

    <div class="mb-4">
        <label class="block text-gray-600">Category</label>
        <input type="text" name="category_name" list="categories" maxlength="100" required
               placeholder="e.g. Rent, Fuel, Utilities" value="<?= e($entry['category_name']) ?>"
               class="w-full p-2 border rounded">
        <datalist id="categories">
            <?php foreach ($categoryNames as $n): ?><option value="<?= e($n) ?>"><?php endforeach; ?>
        </datalist>
        <p class="text-xs text-gray-500 mt-1">Pick an existing category or type a new one.</p>
    </div>

    <div class="mb-4">
        <label class="block text-gray-600">Description</label>
        <textarea name="description" rows="3" maxlength="1000"
                  class="w-full p-2 border rounded"><?= e($entry['description']) ?></textarea>
    </div>

    <div class="mb-4">
        <label class="block text-gray-600">Date</label>
        <input type="date" name="expense_date" required value="<?= e($entry['expense_date']) ?>"
               class="w-full p-2 border rounded">
    </div>

    <div class="flex gap-2">
        <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"><?= e($submitLabel) ?></button>
        <a href="index.php" class="px-4 py-2 border rounded">Cancel</a>
    </div>
</form>
