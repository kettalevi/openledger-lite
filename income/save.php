<?php
/**
 * Shared POST handler for create/edit. Expects $conn; optional $editId.
 * Returns [errors, entry]; redirects on success.
 */
function income_handle_post(PDO $conn, ?int $editId): array
{
    csrf_check();
    $entry = [
        'amount'           => trim((string)($_POST['amount'] ?? '')),
        'source_name'      => clean_text($_POST['source_name'] ?? '', 100),
        'contributor_name' => clean_text($_POST['contributor_name'] ?? '', 100),
        'income_date'      => (string)($_POST['income_date'] ?? ''),
    ];
    $errors = [];
    $amount = parse_amount($entry['amount']);
    if ($amount === null)              $errors[] = 'Enter a valid amount greater than zero (max 2 decimals).';
    if ($entry['source_name'] === '')  $errors[] = 'Source is required.';
    if (!valid_date($entry['income_date'])) $errors[] = 'Enter a valid date.';
    if ($errors) {
        return [$errors, $entry];
    }

    $conn->beginTransaction();
    try {
        $sourceId = find_or_create($conn, 'income_sources', $entry['source_name']);
        if ($editId) {
            $conn->prepare('UPDATE income SET amount=?, source_id=?, contributor_name=?, income_date=? WHERE id=?')
                 ->execute([$amount, $sourceId, $entry['contributor_name'], $entry['income_date'], $editId]);
        } else {
            $conn->prepare('INSERT INTO income (amount, source_id, contributor_name, income_date, created_by) VALUES (?,?,?,?,?)')
                 ->execute([$amount, $sourceId, $entry['contributor_name'], $entry['income_date'], $_SESSION['user_id']]);
        }
        $conn->commit();
    } catch (Throwable $t) {
        $conn->rollBack();
        throw $t;
    }
    flash('success', $editId ? 'Income updated.' : 'Income saved.');
    redirect('income/index.php');
}
