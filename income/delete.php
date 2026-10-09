<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_post();

$stmt = $conn->prepare('DELETE FROM income WHERE id = ?');
$stmt->execute([(int)($_POST['id'] ?? 0)]);
flash($stmt->rowCount() ? 'success' : 'error', $stmt->rowCount() ? 'Income deleted.' : 'Entry not found.');
redirect('income/index.php');
