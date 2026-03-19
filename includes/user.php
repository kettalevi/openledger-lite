<?php
require 'db.php';
//use this file to create a default user. Delete immediately after.
$password = password_hash("admin123", PASSWORD_DEFAULT);

$conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)")
     ->execute(["Admin", "admin@test.com", $password]);

echo "User created!";
