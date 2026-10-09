<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

$f = list_filters('i.income_date', 'i.source_id', 'source_id', ['i.contributor_name', 's.name']);
$stmt = $conn->prepare("
    SELECT i.income_date, s.name, i.contributor_name, i.amount
    FROM income i LEFT JOIN income_sources s ON i.source_id = s.id
    {$f['sql']} ORDER BY i.income_date DESC, i.id DESC");
$stmt->execute($f['params']);

send_csv('income_' . date('Ymd') . '.csv', ['Date', 'Source', 'Contributor', 'Amount'], $stmt->fetchAll(PDO::FETCH_NUM));
