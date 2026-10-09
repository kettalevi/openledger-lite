<?php
/**
 * Shared POST handler for create/edit. Returns [errors, entry]; redirects on success.
 */
function expense_handle_post(PDO $conn, ?int $editId): array
{
    csrf_check();
    $entry = [
        'amount'        => trim((string)($_POST['amount'] ?? '')),
        'category_name' => clean_text($_POST['category_name'] ?? '', 100),
        'description'   => mb_substr(trim((string)($_POST['description'] ?? '')), 0, 1000),
        'expense_date'  => (string)($_POST['expense_date'] ?? ''),
    ];
    $errors = [];
    $amount = parse_amount($entry['amount']);
    if ($amount === null)                $errors[] = 'Enter a valid amount greater than zero (max 2 decimals).';
    if ($entry['category_name'] === '')  $errors[] = 'Category is required.';
    if (!valid_date($entry['expense_date'])) $errors[] = 'Enter a valid date.';
    if ($errors) {
        return [$errors, $entry];
    }

    $conn->beginTransaction();
    try {
        $catId = find_or_create($conn, 'expense_categories', $entry['category_name']);
        if ($editId) {
            $conn->prepare('UPDATE expenses SET amount=?, category_id=?, description=?, expense_date=? WHERE id=?')
                 ->execute([$amount, $catId, $entry['description'], $entry['expense_date'], $editId]);
        } else {
            $conn->prepare('INSERT INTO expenses (amount, category_id, description, expense_date, created_by) VALUES (?,?,?,?,?)')
                 ->execute([$amount, $catId, $entry['description'], $entry['expense_date'], $_SESSION['user_id']]);
        }
        $conn->commit();
    } catch (Throwable $t) {
        $conn->rollBack();
        throw $t;
    }
    flash('success', $editId ? 'Expense updated.' : 'Expense saved.');
    redirect('expenses/index.php');
}
