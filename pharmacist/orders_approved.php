<?php
$required_role = "pharmacist";
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$pharmacist_id = $_SESSION["user_id"];

/* ===== STATS ===== */
$newOrders = $conn->query(
    "SELECT COUNT(*) total 
     FROM orders 
     WHERE order_status='pending'"
)->fetch_assoc()["total"] ?? 0;

$approvedCount = $conn->query(
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE order_status = 'approved'"
    )->fetch_assoc()["total"] ?? 0;

$rejectedToday = $conn->query(
    "SELECT COUNT(*) total FROM orders
    WHERE order_status='rejected'"
    )->fetch_assoc()["total"];

$completedToday = $conn->query(
    "SELECT COUNT(*) total 
     FROM orders 
     WHERE order_status='completed'
     AND DATE(order_date)=CURDATE()"
)->fetch_assoc()["total"] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approved Orders | Pharmacist</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/edit-profile-btn.css">
</head>
<body>

<div class="dashboard-page">

<?php include "../includes/header.php"; ?>

<div class="dashboard-container">

    <!-- ===== STATS ===== -->
    <div class="stats-grid">
        <div class="stat-card">
            <p>Pending</p>
            <h3><?= $newOrders ?></h3>
        </div>

        <div class="stat-card">
                <p>Approved</p>
                <h3><?= $approvedCount ?></h3>
        </div>

        <div class="stat-card">
            <p>Rejected</p>
            <h3><?= $rejectedToday ?></h3>
        </div>
        <div class="stat-card">
            <p>Completed Today</p>
            <h3><?= $completedToday ?></h3>
        </div>
    </div>

    <!-- ===== PANEL ===== -->
    <div class="panel" style="margin-top:20px;">

        <div class="tabs">
            <a href="dashboard.php">Pending</a>
            <a class="active" href="#">Approved</a>
            <a href="order_history.php">History</a>
        </div>

        <h3 style="margin:20px 0;">Approved Orders</h3>

        <?php
        $orders = $conn->query(
            "SELECT 
                o.order_id,
                o.order_date,
                o.allergy_notes,
                o.pharmacist_instructions,
                c.customer_name,
                c.contact_number
            FROM orders o
            JOIN customers c ON o.customer_id = c.customer_id
            WHERE o.order_status='approved'
            ORDER BY o.order_date DESC"
        );

        if ($orders->num_rows === 0):
        ?>
            <p style="color:#777;">No approved orders.</p>
        <?php endif; ?>

        <?php while ($o = $orders->fetch_assoc()): ?>
        <div class="pending-card">

            <div class="pending-header">
                <div>
                    <h4><?= htmlspecialchars($o["customer_name"]) ?></h4>
                    <small>
                        Contact: <?= htmlspecialchars($o["contact_number"]) ?>
                    </small><br>
                    <small>
                        Approved: <?= date("m/d/Y, h:i A", strtotime($o["order_date"])) ?>
                    </small>
                </div>

                <span class="status-badge ready">Ready</span>
            </div>

            <div class="pending-body">
                <strong>Order ID:</strong> <?= $o["order_id"] ?><br><br>

                <!-- MEDICINES -->
                <strong>Medicines:</strong>
                <ul class="medicine-list">
                    <?php
                    $items = $conn->query(
                        "SELECT 
                            m.medicine_name,
                            oi.quantity,
                            oi.price
                        FROM order_items oi
                        JOIN medicines m ON oi.medicine_id = m.medicine_id
                        WHERE oi.order_id = {$o['order_id']}"
                    );

                    while ($item = $items->fetch_assoc()):
                    ?>
                        <li>
                            <strong><?= htmlspecialchars($item["medicine_name"]) ?></strong><br>
                            Qty: <?= $item["quantity"] ?> × <?= $item["price"] ?>฿
                        </li>
                    <?php endwhile; ?>
                </ul>

                <hr>

                <!-- ALLERGY -->
                <p>
                    <strong>Allergy Check:</strong><br>
                    <?= nl2br(htmlspecialchars($o["allergy_notes"] ?: "-")) ?>
                </p>

                <!-- INSTRUCTIONS -->
                <p>
                    <strong>Pharmacist Instructions:</strong><br>
                    <?= nl2br(htmlspecialchars($o["pharmacist_instructions"] ?: "-")) ?>
                </p>

            </div>
        </div>
        <?php endwhile; ?>

    </div>
</div>
</div>
<script src="../assets/js/darkmode.js"></script>
</body>
</html>
