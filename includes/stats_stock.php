<?php
require_once "../config/db.php";

$today = date("Y-m-d");
$soon  = date("Y-m-d", strtotime("+60 days"));

/* TOTAL MEDICINES */
$totalMedicines = $conn->query(
    "SELECT COUNT(*) total FROM medicines"
)->fetch_assoc()["total"] ?? 0;

/* IN STOCK */
$inStock = $conn->query(
    "SELECT COUNT(*) total
     FROM medicines
     WHERE quantity > reorder_level
       AND expiry_date > '$soon'"
)->fetch_assoc()["total"] ?? 0;

/* LOW STOCK */
$lowStock = $conn->query(
    "SELECT COUNT(*) total
     FROM medicines
     WHERE quantity <= reorder_level
       AND expiry_date > '$soon'"
)->fetch_assoc()["total"] ?? 0;

/* EXPIRE SOON */
$expireSoon = $conn->query(
    "SELECT COUNT(*) total
     FROM medicines
     WHERE expiry_date >= '$today'
       AND expiry_date <= '$soon'"
)->fetch_assoc()["total"] ?? 0;
?>


<div class="stats-grid">

    <div class="stat-card">
        <p>Total Medicines</p>
        <h3><?= $totalMedicines ?></h3>
    </div>

    <div class="stat-card success">
        <p>In Stock</p>
        <h3><?= $inStock ?></h3>
    </div>

    <div class="stat-card warning">
        <p>Low Stock Alerts</p>
        <h3><?= $lowStock ?></h3>
    </div>

    <div class="stat-card danger">
        <p>Expire Soon</p>
        <h3><?= $expireSoon ?></h3>
    </div>

</div>