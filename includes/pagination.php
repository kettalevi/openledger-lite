<?php
/** Expects $page, $pages (ints). Preserves current filters. */
if ($pages > 1): ?>
<div class="flex items-center justify-between mt-4 text-sm">
    <span class="text-gray-500">Page <?= $page ?> of <?= $pages ?></span>
    <div class="flex gap-2">
        <?php if ($page > 1): ?>
            <a class="px-3 py-1 border rounded bg-white hover:bg-gray-50" href="<?= e(qs(['page' => $page - 1])) ?>">&larr; Prev</a>
        <?php endif; ?>
        <?php if ($page < $pages): ?>
            <a class="px-3 py-1 border rounded bg-white hover:bg-gray-50" href="<?= e(qs(['page' => $page + 1])) ?>">Next &rarr;</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
