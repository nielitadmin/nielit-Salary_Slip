<?php
session_start();
require 'db.php';

// 🔒 Admin login check
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// 🧩 Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("Invalid request.");
}

$id = intval($_GET['id']);

try {
    // Check if record exists
    $stmt = $pdo->prepare("SELECT * FROM slips WHERE id = ?");
    $stmt->execute([$id]);
    $slip = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$slip) {
        exit("Slip not found.");
    }

    // Delete the slip
    $del = $pdo->prepare("DELETE FROM slips WHERE id = ?");
    $del->execute([$id]);

    // Optional: log deletion for safety (comment out if not needed)
    // error_log("Slip deleted: ID=$id by Admin ".$_SESSION['admin_id']);

    // Redirect back
    header("Location: dashboard.php");
    exit;

} catch (Exception $e) {
    exit("Error deleting slip: " . htmlspecialchars($e->getMessage()));
}
?>
