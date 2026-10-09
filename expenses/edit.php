<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once 'save.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('
    SELECT e.amount, e.description, e.expense_date, c.name AS category_name
    FROM expenses e LEFT JOIN expense_categories c ON c.id = e.category_id WHERE e.id = ?');
$stmt->execute([$id]);
$entry = $stmt->fetch();
if (!$entry) {
    flash('error', 'Expense not found.');
    redirect('expenses/index.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$errors, $entry] = expense_handle_post($conn, $id);
}

$categoryNames = $conn->query('SELECT name FROM expense_categories ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);
$current = 'expenses';
$pageTitle = 'Edit Expense';
$formTitle = 'Edit Expense';
$submitLabel = 'Update Expense';
require_once '../includes/header.php';
require 'form.php';
require_once '../includes/footer.php';
