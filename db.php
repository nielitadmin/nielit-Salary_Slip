<?php 
/**
 * db.php — Central database connection file for
 * NIELIT Bhubaneswar Salary Slip Generator
 * Updated for Hostinger MySQL
 * Developed by Kumar Dinesh Behera
 */

// -------------------------------
// ✅ Database Configuration
// -------------------------------
$DB_HOST = 'mysql.hostinger.in'; // Hostinger uses 'localhost' for MySQL
$DB_NAME = 'u664913565_nielit_salary'; 
$DB_USER = 'u664913565_nielit_salary';
$DB_PASS = 'Nielitbbsr@2025'; // 🔒 Replace with the real DB password from Hostinger

// -------------------------------
// ✅ Create PDO Connection
// -------------------------------
try {
    $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,          // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // Fetch rows as associative arrays
        PDO::ATTR_EMULATE_PREPARES => false,                  // Use real prepared statements
    ]);
} catch (PDOException $e) {
    // 🔴 Graceful Error Display
    echo "<div style='font-family: Arial, sans-serif; color: red; margin: 2rem;'>
            <h2>🚨 Database Connection Failed</h2>
            <p><strong>Host:</strong> {$DB_HOST}</p>
            <p><strong>Database:</strong> {$DB_NAME}</p>
            <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
            <p>Please check your Hostinger database credentials and make sure the database exists.</p>
          </div>";
    exit;
}

// -------------------------------
// ✅ Optional: Verify Connection
// -------------------------------
// Uncomment this line to test connection:
// echo "<p style='color:green;font-family:Arial;'>✅ Connected to database '{$DB_NAME}' successfully!</p>";

?>
