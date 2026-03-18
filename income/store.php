<?php
require_once '../includes/db.php';

$amount = $_POST['amount'];
$source_name = trim($_POST['source_name']);
$contributor = $_POST['contributor_name'];
$date = $_POST['income_date'];
$user_id = $_SESSION['user_id'];

// 1. Check if source exists
$stmt = $conn->prepare("SELECT id FROM income_sources WHERE name = ?");
$stmt->execute([$source_name]);
$source = $stmt->fetch();

// 2. If not, create it
if (!$source) {
    $stmt = $conn->prepare("INSERT INTO income_sources (name) VALUES (?)");
    $stmt->execute([$source_name]);
    $source_id = $conn->lastInsertId();
} else {
    $source_id = $source['id'];
}

// 3. Insert income
$stmt = $conn->prepare("
    INSERT INTO income (amount, source_id, contributor_name, income_date, created_by)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([$amount, $source_id, $contributor, $date, $user_id]);

header("Location: index.php");
exit;
