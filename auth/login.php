<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!empty($_SESSION['user_id'])) {
    redirect('dashboard/index.php');
}

$error = '';
$email = '';
$loggedOut = isset($_GET['logged_out']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = clean_text($_POST['email'] ?? '', 100);
    $password = (string)($_POST['password'] ?? '');

    // Basic brute-force throttle: 5 failures => 60s lockout (per session).
    $lockedUntil = $_SESSION['login_locked_until'] ?? 0;
    if ($lockedUntil > time()) {
        $error = 'Too many failed attempts. Try again in ' . ($lockedUntil - time()) . ' seconds.';
    } else {
        $stmt = $conn->prepare('SELECT id, password FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Always run a hash check so timing doesn't reveal whether the email exists.
        $hash = $user['password'] ?? '$2y$10$usesomesillystringforsaltOabcdefghijklmnopqrstuvwxyzABC';
        $ok = password_verify($password, $hash) && $user;

        if ($ok) {
            session_regenerate_id(true);
            unset($_SESSION['login_fails'], $_SESSION['login_locked_until']);
            $_SESSION['user_id'] = (int)$user['id'];
            if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                $conn->prepare('UPDATE users SET password = ? WHERE id = ?')
                     ->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
            }
            redirect('dashboard/index.php');
        }

        $_SESSION['login_fails'] = ($_SESSION['login_fails'] ?? 0) + 1;
        if ($_SESSION['login_fails'] >= 5) {
            $_SESSION['login_locked_until'] = time() + 60;
            $_SESSION['login_fails'] = 0;
        }
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · OpenLedger Lite</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

<form method="POST" class="bg-white p-6 rounded shadow w-full max-w-sm">
    <h2 class="text-xl mb-4 font-bold">OpenLedger Lite — Login</h2>

    <?php if ($loggedOut && !$error): ?>
        <p class="text-green-600 mb-3">You have been logged out.</p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="text-red-600 mb-3"><?= e($error) ?></p>
    <?php endif; ?>

    <?= csrf_field() ?>
    <input type="email" name="email" placeholder="Email" required autofocus
           value="<?= e($email) ?>" class="w-full mb-3 p-2 border rounded">
    <input type="password" name="password" placeholder="Password" required
           class="w-full mb-3 p-2 border rounded">

    <button class="bg-blue-600 text-white w-full p-2 rounded hover:bg-blue-700">Login</button>
</form>

</body>
</html>
