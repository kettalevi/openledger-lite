<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once 'save.php';

$errors = [];
$entry = ['amount' => '', 'category_name' => '', 'description' => '', 'expense_date' => date('Y-m-d')];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$errors, $entry] = expense_handle_post($conn, null);
}

$categoryNames = $conn->query('SELECT name FROM expense_categories ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);
$current = 'expenses';
$pageTitle = 'Add Expense';
$formTitle = 'Add Expense';
$submitLabel = 'Save Expense';
require_once '../includes/header.php';
require 'form.php';
require_once '../includes/footer.php';
