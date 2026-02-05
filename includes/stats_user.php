<?php
require_once "../config/db.php";

/* TOTAL USERS */
$totalUsers = $conn->query(
    "SELECT COUNT(*) total FROM users"
)->fetch_assoc()["total"] ?? 0;

/* ADMINS */
$totalAdmins = $conn->query(
    "SELECT COUNT(*) total FROM users WHERE role = 'admin'"
)->fetch_assoc()["total"] ?? 0;

/* PHARMACISTS */
$totalPharmacists = $conn->query(
    "SELECT COUNT(*) total FROM users WHERE role = 'pharmacist'"
)->fetch_assoc()["total"] ?? 0;

/* STAFF */
$totalStaff = $conn->query(
    "SELECT COUNT(*) total FROM users WHERE role = 'staff'"
)->fetch_assoc()["total"] ?? 0;
?>

<div class="stats-grid">

    <div class="stat-card">
        <p>Total Users</p>
        <h3><?= $totalUsers ?></h3>
    </div>

    <div class="stat-card success">
        <p>Admins</p>
        <h3><?= $totalAdmins ?></h3>
    </div>

    <div class="stat-card">
        <p>Pharmacists</p>
        <h3><?= $totalPharmacists ?></h3>
    </div>

    <div class="stat-card">
        <p>Staff</p>
        <h3><?= $totalStaff ?></h3>
    </div>

</div>