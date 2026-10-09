<?php
require_once '../includes/functions.php';

// Logout requires POST + CSRF token so other sites can't log users out.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard/index.php');
}
csrf_check();

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

header('Location: ' . url('auth/login.php?logged_out=1'));
exit;
