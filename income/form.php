<?php
/** Shared add/edit form. Expects $entry (array), $errors (array), $formTitle, $submitLabel, $sourceNames. */
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
        <label class="block text-gray-600">Source</label>
        <input type="text" name="source_name" list="sources" maxlength="100" required
               placeholder="e.g. Donations, Sales" value="<?= e($entry['source_name']) ?>"
               class="w-full p-2 border rounded">
        <datalist id="sources">
            <?php foreach ($sourceNames as $n): ?><option value="<?= e($n) ?>"><?php endforeach; ?>
        </datalist>
        <p class="text-xs text-gray-500 mt-1">Pick an existing source or type a new one.</p>
    </div>

    <div class="mb-4">
        <label class="block text-gray-600">Contributor Name</label>
        <input type="text" name="contributor_name" maxlength="100"
               value="<?= e($entry['contributor_name']) ?>" class="w-full p-2 border rounded">
    </div>

    <div class="mb-4">
        <label class="block text-gray-600">Date</label>
        <input type="date" name="income_date" required value="<?= e($entry['income_date']) ?>"
               class="w-full p-2 border rounded">
    </div>

    <div class="flex gap-2">
        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"><?= e($submitLabel) ?></button>
        <a href="index.php" class="px-4 py-2 border rounded">Cancel</a>
    </div>
</form>
