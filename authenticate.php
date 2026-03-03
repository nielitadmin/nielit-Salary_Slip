<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    header('Location: login.php?err=1');
    exit;
}

try {
    // ✅ Check for existing admin record
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        $stored = $user['password'];

        // ✅ 1. Match bcrypt (password_hash / password_verify)
        if (password_verify($password, $stored)) {
            $valid = true;
        }
        // ✅ 2. Match older SHA256 hashes (if any)
        elseif (hash('sha256', $password) === $stored) {
            $valid = true;
        }
        // ✅ 3. Match plain password (legacy fallback)
        elseif ($password === $stored) {
            $valid = true;
        } else {
            $valid = false;
        }

        if ($valid) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['fullname'] ?: $user['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            header('Location: login.php?err=1');
            exit;
        }
    } else {
        header('Location: login.php?err=1');
        exit;
    }
} catch (PDOException $e) {
    die("<h3 style='color:red;'>Database Error: " . htmlspecialchars($e->getMessage()) . "</h3>");
}
?>
