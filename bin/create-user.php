<?php
// CLI only: php bin/create-user.php "Name" email@example.com [password]
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require __DIR__ . '/../includes/db.php';

[$script, $name, $email] = $argv + [null, null, null];
$password = $argv[3] ?? null;
if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Usage: php bin/create-user.php \"Name\" email@example.com [password]\n");
    exit(1);
}
if ($password === null) {
    fwrite(STDOUT, 'Password (min 8 chars): ');
    $password = trim((string)fgets(STDIN));
}
if (strlen($password) < 8) {
    fwrite(STDERR, "Password must be at least 8 characters.\n");
    exit(1);
}

try {
    $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)')
         ->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
    echo "User $email created.\n";
} catch (PDOException $e) {
    fwrite(STDERR, ($e->getCode() == 23000 ? "A user with that email already exists." : $e->getMessage()) . "\n");
    exit(1);
}
