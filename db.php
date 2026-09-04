<?php
// ============================================================
//  db.php — Database Connection (Procedural mysqli)
//  EWU Student Academic Portal
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ewu_portal');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die('<div style="font-family:monospace;background:#1a0000;color:#ff6b6b;padding:20px;border-radius:8px;">
        <strong>⚠ Database Connection Failed</strong><br>
        Error: ' . mysqli_connect_error() . '<br><br>
        <em>Steps to fix:</em><br>
        1. Make sure XAMPP MySQL service is running.<br>
        2. Import <code>database.sql</code> in phpMyAdmin.<br>
        3. Confirm credentials in <code>db.php</code>.
    </div>');
}

mysqli_set_charset($conn, 'utf8mb4');
?>
