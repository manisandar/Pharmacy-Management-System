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

$supplier_id   = $_POST["supplier_id"];
$supplier_name = trim($_POST["supplier_name"]);
$contact_number = trim($_POST["contact_number"]);

if (empty($supplier_id) || empty($supplier_name)) {
    header("Location: suppliers.php?error=missing_fields");
    exit;
}

$stmt = $conn->prepare(
    "UPDATE suppliers 
     SET supplier_name = ?, contact_number = ?
     WHERE supplier_id = ?"
);
$stmt->bind_param("ssi", $supplier_name, $contact_number, $supplier_id);

if ($stmt->execute()) {
    header("Location: suppliers.php?success=supplier_updated");
} else {
    header("Location: suppliers.php?error=update_failed");
}

$stmt->close();
$conn->close();
exit;
