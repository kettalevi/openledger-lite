<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_post();

$stmt = $conn->prepare('DELETE FROM expenses WHERE id = ?');
$stmt->execute([(int)($_POST['id'] ?? 0)]);
flash($stmt->rowCount() ? 'success' : 'error', $stmt->rowCount() ? 'Expense deleted.' : 'Entry not found.');
redirect('expenses/index.php');
