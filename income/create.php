<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once 'save.php';

$errors = [];
$entry = ['amount' => '', 'source_name' => '', 'contributor_name' => '', 'income_date' => date('Y-m-d')];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$errors, $entry] = income_handle_post($conn, null);
}

$sourceNames = $conn->query('SELECT name FROM income_sources ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);
$current = 'income';
$pageTitle = 'Add Income';
$formTitle = 'Add Income';
$submitLabel = 'Save Income';
require_once '../includes/header.php';
require 'form.php';
require_once '../includes/footer.php';
