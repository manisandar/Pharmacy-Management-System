<?php
session_start();
require_once "../config/db.php";
require_once "../includes/auth_check.php";

// Only admin can add medicine
if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard.php");
    exit;
}

// Handle POST request only
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

// Collect form data
$medicine_name   = trim($_POST["medicine_name"]);
$chemical_name   = trim($_POST["chemical_name"]);
$dosage_form     = trim($_POST["dosage_form"]);
$price_per_unit  = $_POST["price_per_unit"];
$quantity        = $_POST["quantity"];
$reorder_level   = $_POST["reorder_level"];
$expiry_date     = $_POST["expiry_date"];
$supplier_id     = $_POST["supplier_id"];

// Basic validation
if (
    empty($medicine_name) ||
    empty($dosage_form) ||
    empty($price_per_unit) ||
    empty($quantity) ||
    empty($reorder_level) ||
    empty($expiry_date) ||
    empty($supplier_id)
) {
    header("Location: dashboard.php?error=missing_fields");
    exit;
}

// Insert into database
$stmt = $conn->prepare("
    INSERT INTO medicines
    (medicine_name, chemical_name, dosage_form, price_per_unit, quantity, reorder_level, expiry_date, supplier_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssdiisi",
    $medicine_name,
    $chemical_name,
    $dosage_form,
    $price_per_unit,
    $quantity,
    $reorder_level,
    $expiry_date,
    $supplier_id
);

if ($stmt->execute()) {
    // Success
    header("Location: dashboard.php?success=medicine_added");
} else {
    // Error
    header("Location: dashboard.php?error=insert_failed");
}

$conn->close();
exit;
