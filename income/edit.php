<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once 'save.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('
    SELECT i.amount, i.contributor_name, i.income_date, s.name AS source_name
    FROM income i LEFT JOIN income_sources s ON s.id = i.source_id WHERE i.id = ?');
$stmt->execute([$id]);
$entry = $stmt->fetch();
if (!$entry) {
    flash('error', 'Income entry not found.');
    redirect('income/index.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$errors, $entry] = income_handle_post($conn, $id);
}

$sourceNames = $conn->query('SELECT name FROM income_sources ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);
$current = 'income';
$pageTitle = 'Edit Income';
$formTitle = 'Edit Income';
$submitLabel = 'Update Income';
require_once '../includes/header.php';
require 'form.php';
require_once '../includes/footer.php';
