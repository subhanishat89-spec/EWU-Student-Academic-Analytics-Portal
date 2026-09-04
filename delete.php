<?php
// ============================================================
//  delete.php — Record Deletion Handler
//  EWU Student Academic Portal
// ============================================================

require_once 'db.php';

// ── Validate ID ─────────────────────────────────────────────
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php?status=error&msg=Invalid+record+ID');
    exit;
}

// ── Delete query ────────────────────────────────────────────
$sql = "DELETE FROM academic_records WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    if (mysqli_affected_rows($conn) > 0) {
        header('Location: index.php?status=deleted');
    } else {
        header('Location: index.php?status=error&msg=Record+not+found');
    }
} else {
    $err = urlencode('Delete failed: ' . mysqli_error($conn));
    header("Location: index.php?status=error&msg=$err");
}

mysqli_close($conn);
exit;
?>
