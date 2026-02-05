<?php
session_start();
require_once "../config/db.php";
require_once "../includes/auth_check.php";

if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

// Get data
$medicine_id     = $_POST["medicine_id"];
$medicine_name   = trim($_POST["medicine_name"]);
$chemical_name   = trim($_POST["chemical_name"]);
$dosage_form     = $_POST["dosage_form"];
$price_per_unit  = $_POST["price_per_unit"];
$quantity        = $_POST["quantity"];
$reorder_level   = $_POST["reorder_level"];
$expiry_date     = $_POST["expiry_date"];
$supplier_id     = $_POST["supplier_id"];

if (
    empty($medicine_id) ||
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

// Update query
$stmt = $conn->prepare("
    UPDATE medicines SET
        medicine_name = ?,
        chemical_name = ?,
        dosage_form = ?,
        price_per_unit = ?,
        quantity = ?,
        reorder_level = ?,
        expiry_date = ?,
        supplier_id = ?
    WHERE medicine_id = ?
");

$stmt->bind_param(
    "sssdiissi",
    $medicine_name,
    $chemical_name,
    $dosage_form,
    $price_per_unit,
    $quantity,
    $reorder_level,
    $expiry_date,
    $supplier_id,
    $medicine_id
);

if ($stmt->execute()) {
    header("Location: dashboard.php?success=medicine_updated");
} else {
    header("Location: dashboard.php?error=update_failed");
}

$stmt->close();
$conn->close();
exit;
