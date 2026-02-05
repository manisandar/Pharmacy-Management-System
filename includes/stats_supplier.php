<?php
require_once "../config/db.php";

/* TOTAL SUPPLIERS */
$totalSuppliers = $conn->query(
    "SELECT COUNT(*) total FROM suppliers"
)->fetch_assoc()["total"] ?? 0;

/* ACTIVE SUPPLIERS */
$activeSuppliers = $conn->query(
    "SELECT COUNT(DISTINCT supplier_id) total
     FROM medicines
     WHERE supplier_id IS NOT NULL"
)->fetch_assoc()["total"] ?? 0;
?>


<div class="stats-grid">

    <div class="stat-card">
        <p>Total Suppliers</p>
        <h3><?= $totalSuppliers ?></h3>
    </div>

    <div class="stat-card success">
        <p>Active Suppliers</p>
        <h3><?= $activeSuppliers ?></h3>
    </div>

</div>