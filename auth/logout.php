<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: /dashboard/index.php");
        exit;
    } else {
        $error = "Invalid login";
    }
}
if (isset($_GET['logged_out'])) {
    echo "<p class='text-green-600 mb-3'>You have been logged out.</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<form method="POST" class="bg-white p-6 rounded shadow w-80">
    <h2 class="text-xl mb-4 font-bold">Login</h2>

    <?php if (!empty($error)) echo "<p class='text-red-500'>$error</p>"; ?>

    <input type="email" name="email" placeholder="Email" required class="w-full mb-3 p-2 border rounded">
    <input type="password" name="password" placeholder="Password" required class="w-full mb-3 p-2 border rounded">

    <button class="bg-blue-600 text-white w-full p-2 rounded">Login</button>
</form>

</body>
</html>
