<?php
$staff_id = $_SESSION["user_id"];

$pendingCount = $conn->query(
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE staff_id = $staff_id AND order_status = 'pending'"
)->fetch_assoc()["total"] ?? 0;

$approvedCount = $conn->query(
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE staff_id = $staff_id AND order_status = 'approved'"
)->fetch_assoc()["total"] ?? 0;

$completedToday = $conn->query(
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE staff_id = $staff_id
       AND order_status = 'completed'
       AND DATE(order_date) = CURDATE()"
)->fetch_assoc()["total"] ?? 0;
?>

<div class="stats-grid">
    <div class="stat-card">
        <p>Pending Orders</p>
        <h3><?= $pendingCount ?></h3>
    </div>

    <div class="stat-card">
        <p>Ready for Payment</p>
        <h3><?= $approvedCount ?></h3>
    </div>

    <div class="stat-card">
        <p>Completed Today</p>
        <h3><?= $completedToday ?></h3>
    </div>
</div>
