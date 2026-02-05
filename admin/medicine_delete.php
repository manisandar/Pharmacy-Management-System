<?php
session_start();
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
