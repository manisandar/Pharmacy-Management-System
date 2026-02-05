<?php
session_start();
require_once "../includes/auth_check.php";
require_once "../config/db.php";

// Only admin
if ($_SESSION["role"] !== "admin") {
    header("Location: suppliers.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: suppliers.php");
    exit;
}

$supplier_name = trim($_POST["supplier_name"]);
$contact_number = trim($_POST["contact_number"]);

if (empty($supplier_name)) {
    header("Location: suppliers.php?error=missing_fields");
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO suppliers (supplier_name, contact_number) VALUES (?, ?)"
);
$stmt->bind_param("ss", $supplier_name, $contact_number);

if ($stmt->execute()) {
    header("Location: suppliers.php?success=supplier_added");
} else {
    header("Location: suppliers.php?error=insert_failed");
}

$stmt->close();
$conn->close();
exit;
