<?php
session_start();
require_once "../includes/auth_check.php";
require_once "../config/db.php";

// Only admin
if ($_SESSION["role"] !== "admin") {
    header("Location: suppliers.php");
    exit;
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: suppliers.php?error=invalid_id");
    exit;
}

$supplier_id = $_GET["id"];

// Check if supplier is used in medicines
$check = $conn->prepare(
    "SELECT COUNT(*) FROM medicines WHERE supplier_id = ?"
);
$check->bind_param("i", $supplier_id);
$check->execute();
$check->bind_result($count);
$check->fetch();
$check->close();

if ($count > 0) {
    // Supplier is in use
    header("Location: suppliers.php?error=supplier_in_use");
    exit;
}

// Safe to delete
$stmt = $conn->prepare(
    "DELETE FROM suppliers WHERE supplier_id = ?"
);
$stmt->bind_param("i", $supplier_id);

if ($stmt->execute()) {
    header("Location: suppliers.php?success=supplier_deleted");
} else {
    header("Location: suppliers.php?error=delete_failed");
}

$stmt->close();
$conn->close();
exit;
