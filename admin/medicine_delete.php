<?php
// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/db.php";
require_once "../includes/auth_check.php";

// Only admin can delete
if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard.php");
    exit;
}

// Validate ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: dashboard.php?error=invalid_id");
    exit;
}

$medicine_id = $_GET["id"];

// Check if medicine is used in any orders
$check_stmt = $conn->prepare("SELECT COUNT(*) as order_count FROM order_items WHERE medicine_id = ?");
$check_stmt->bind_param("i", $medicine_id);
$check_stmt->execute();
$result = $check_stmt->get_result();
$row = $result->fetch_assoc();
$check_stmt->close();

if ($row['order_count'] > 0) {
    // Medicine is in use, cannot delete
    header("Location: dashboard.php?error=medicine_in_use");
    exit;
}

// Delete query
$stmt = $conn->prepare("DELETE FROM medicines WHERE medicine_id = ?");
$stmt->bind_param("i", $medicine_id);

if ($stmt->execute()) {
    header("Location: dashboard.php?success=medicine_deleted");
} else {
    header("Location: dashboard.php?error=delete_failed");
}

$stmt->close();
$conn->close();
exit;
