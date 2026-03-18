<?php
require_once '../includes/db.php';

$amount = $_POST['amount'];
$category_name = trim($_POST['category_name']);
$description = $_POST['description'];
$date = $_POST['expense_date'];
$user_id = $_SESSION['user_id'];

// 1. Check if category exists
$stmt = $conn->prepare("SELECT id FROM expense_categories WHERE name = ?");
$stmt->execute([$category_name]);
$category = $stmt->fetch();

// 2. Create if not exists
if (!$category) {
    $stmt = $conn->prepare("INSERT INTO expense_categories (name) VALUES (?)");
    $stmt->execute([$category_name]);
    $category_id = $conn->lastInsertId();
} else {
    $category_id = $category['id'];
}

// 3. Insert expense
$stmt = $conn->prepare("
    INSERT INTO expenses (amount, category_id, description, expense_date, created_by)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([$amount, $category_id, $description, $date, $user_id]);

header("Location: index.php");
exit;
