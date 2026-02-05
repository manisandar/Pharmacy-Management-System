<?php
$required_role = "staff";
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$staff_id = $_SESSION["user_id"];

/* ===== STATS ===== */
$pendingCount = $conn->query(
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE order_status = 'pending'"
)->fetch_assoc()["total"] ?? 0;

$approvedCount = $conn->query(
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE order_status = 'approved'"
)->fetch_assoc()["total"] ?? 0;

$completedToday = $conn->query(
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE order_status = 'completed'
       AND DATE(order_date) = CURDATE()"
)->fetch_assoc()["total"] ?? 0;

$rejectedCount = $conn->query(
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE order_status = 'rejected'"
)->fetch_assoc()["total"] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ready For Payment | PharmFlow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/edit-profile-btn.css">
</head>
<body>

<div class="dashboard-page">

    <!-- HEADER -->
    <?php include "../includes/header.php"; ?>

    <div class="dashboard-container">

        <!-- STATS -->
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
                <p>Rejected Orders</p>
                <h3><?= $rejectedCount ?></h3>
            </div>

            <div class="stat-card">
                <p>Completed Today</p>
                <h3><?= $completedToday ?></h3>
            </div>
        </div>

        <!-- READY ORDERS PANEL -->
        <div class="panel" style="margin-top:20px;">

            <div class="tabs">
                <a href="dashboard.php">Customers</a>
                <a href="pending_orders.php">Pending</a>
                <a class="active" href="#">Ready For Payment</a>
                <a href="order_history.php">History</a>
            </div>

            <div class="panel-header">
                <h3>Ready Orders</h3>
            </div>

            <?php
            $orders = $conn->query(
                "SELECT 
                    o.order_id,
                    o.order_date,
                    o.total_amount,
                    o.pharmacist_instructions,
                    o.allergy_notes,
                    c.customer_name,
                    c.contact_number,
                    c.customer_id
                 FROM orders o
                 JOIN customers c ON o.customer_id = c.customer_id
                 WHERE o.order_status = 'approved'
                 ORDER BY o.order_date DESC"
            );

            if ($orders->num_rows === 0):
            ?>
                <p style="padding:20px; color:#777;">No orders ready for payment.</p>
            <?php endif; ?>

            <?php while ($o = $orders->fetch_assoc()): ?>
                <div class="pending-card">

                    <div class="pending-header">
                        <div>
                            <h4><?= htmlspecialchars($o["customer_name"]) ?></h4>
                            <small>
                                ID: <?= $o["customer_id"] ?> |
                                Contact: <?= htmlspecialchars($o["contact_number"]) ?>
                            </small><br>
                            <small>
                                Created: <?= date("m/d/Y, h:i A", strtotime($o["order_date"])) ?>
                            </small>
                        </div>

                        <span class="status-badge ready">READY</span>
                    </div>

                    <div class="pending-body">
                        <strong>Order ID:</strong> <?= $o["order_id"] ?><br><br>
                        <ul class="medicine-list">
                            <?php
                            $items = $conn->query(
                                "SELECT m.medicine_name, oi.quantity, oi.price
                                 FROM order_items oi
                                 JOIN medicines m ON oi.medicine_id = m.medicine_id
                                 WHERE oi.order_id = {$o['order_id']}"
                            );
                            while ($item = $items->fetch_assoc()):
                            ?>
                                <li>
                                    <strong><?= htmlspecialchars($item["medicine_name"]) ?></strong><br>
                                    Tablet — Qty: <?= $item["quantity"] ?> × <?= number_format($item["price"],2) ?>฿
                                </li>
                            <?php endwhile; ?>
                        </ul>

                        <p><strong>Pharmacist Instructions:</strong><br>
                            <?= nl2br(htmlspecialchars($o["pharmacist_instructions"] ?? "—")) ?>
                        </p>

                        <p><strong>Allergy Check:</strong>
                            <?= htmlspecialchars($o["allergy_notes"] ?? "—") ?>
                        </p>

                        <div class="order-total">
                            Total Amount: ฿ <?= number_format($o["total_amount"], 2) ?>
                        </div>

                        <div style="margin-top:12px;">
                            <button class="btn-primary"
                                    onclick="openBillModal(<?= $o['order_id'] ?>)">
                                View Bill
                            </button>

                            <!-- <a href="view_bill.php?order_id=<?= $o['order_id'] ?>"
                               class="btn-primary"
                               style="padding:6px 14px; font-size:13px;">
                                View Bill
                            </a>

                            <a href="mark_completed.php?order_id=<?= $o['order_id'] ?>"
                               class="btn-success"
                               style="padding:6px 14px; font-size:13px;">
                                Mark Completed
                            </a> -->
                        </div>
                    </div>

                </div>
            <?php endwhile; ?>

        </div>
    </div>
</div>

<div id="billModal" class="modal-overlay" style="display:none;">
    <div class="modal-box large">

        <div class="modal-header">
            <h3>Payment Bill</h3>
            <button class="modal-close" onclick="closeBillModal()">×</button>
        </div>

        <div id="billContent">
            <!-- BILL CONTENT LOADS HERE -->
        </div>

        <div class="modal-actions">
            <button class="btn-primary" onclick="printBill()">
                Print Bill & Mark as Completed
            </button>
        </div>

    </div>
</div>
<script>
let currentOrderId = null;

function openBillModal(orderId) {
    currentOrderId = orderId;
    document.getElementById("billModal").style.display = "flex";

    fetch("load_bill.php?order_id=" + orderId)
        .then(res => res.text())
        .then(html => {
            document.getElementById("billContent").innerHTML = html;
        });
}

function closeBillModal() {
    document.getElementById("billModal").style.display = "none";
}

function printBill() {
    fetch("mark_completed.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "order_id=" + currentOrderId
    }).then(() => {
        window.print();
        location.reload(); // refresh ready list
    });
}

const toggleBtn = document.getElementById("themeToggle");
const body = document.body;

/* Load saved theme */
if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark-mode");
    toggleBtn.textContent = "☀️";
}

/* Toggle */
toggleBtn.addEventListener("click", () => {
    body.classList.toggle("dark-mode");

    if (body.classList.contains("dark-mode")) {
        localStorage.setItem("theme", "dark");
        toggleBtn.textContent = "☀️";
    } else {
        localStorage.setItem("theme", "light");
        toggleBtn.textContent = "🌙";
    }
});
</script>


</body>
</html>
