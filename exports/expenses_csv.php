<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

$f = list_filters('e.expense_date', 'e.category_id', 'category_id', ['e.description', 'c.name']);
$stmt = $conn->prepare("
    SELECT e.expense_date, c.name, e.description, e.amount
    FROM expenses e LEFT JOIN expense_categories c ON e.category_id = c.id
    {$f['sql']} ORDER BY e.expense_date DESC, e.id DESC");
$stmt->execute($f['params']);

send_csv('expenses_' . date('Ymd') . '.csv', ['Date', 'Category', 'Description', 'Amount'], $stmt->fetchAll(PDO::FETCH_NUM));
