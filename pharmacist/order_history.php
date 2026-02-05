<?php
$required_role = "pharmacist";
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$pharmacist_id = $_SESSION["user_id"];

/* ===== STATS ===== */
$pendingCount = $conn->query(
    "SELECT COUNT(*) total
     FROM orders
     WHERE order_status = 'pending'"
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
     WHERE order_status = 'completed'
     AND DATE(order_date) = CURDATE()"
)->fetch_assoc()["total"] ?? 0;

/* ===== HISTORY FILTER ===== */
$where = "WHERE o.order_status IN ('completed','rejected')";

if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $from = $_GET['from_date'];
    $to   = $_GET['to_date'];
    $where .= " AND DATE(o.order_date) BETWEEN '$from' AND '$to'";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pharmacist History | PharmFlow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/edit-profile-btn.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>

<div class="dashboard-page">

<?php include "../includes/header.php"; ?>

<div class="dashboard-container">

    <!-- ===== STATS ===== -->
    <div class="stats-grid">
        <div class="stat-card">
            <p>Pending</p>
            <h3><?= $pendingCount ?></h3>
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

    <!-- ===== HISTORY PANEL ===== -->
    <div class="panel" style="margin-top:20px;">

        <div class="tabs">
            <a href="dashboard.php">Pending</a>
            <a href="orders_approved.php">Approved</a>
            <a class="active" href="#">History</a>
        </div>

        <div class="panel-header">
            <h3>Order History</h3>
        </div>

        <!-- FILTER -->
        <form method="GET" style="margin-bottom:20px;">
            <div class="form-row">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="text"
                            name="from_date"
                            id="from_date"
                            class="date-input"
                            value="<?= $_GET['from_date'] ?? '' ?>"
                            placeholder="Select start date">
                </div>

                <div class="form-group">
                    <label>To Date</label>
                    <input type="text"
                            name="to_date"
                            id="to_date"
                            class="date-input"
                            value="<?= $_GET['to_date'] ?? '' ?>"
                            placeholder="Select end date">
                </div>

                <div class="form-group" style="align-self:end;">
                    <button type="submit" class="btn-filter">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        <?php
        $orders = $conn->query(
            "SELECT 
                o.order_id,
                o.order_date,
                o.total_amount,
                o.order_status,
                o.prescription_note,
                o.allergy_notes,
                o.pharmacist_instructions,
                c.customer_name,
                c.contact_number,
                c.customer_id
             FROM orders o
             JOIN customers c ON o.customer_id = c.customer_id
             $where
             ORDER BY o.order_date DESC"
        );

        if ($orders->num_rows === 0):
        ?>
            <p style="padding:20px; color:#777;">
                No order history found.
            </p>
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
                        Date: <?= date("m/d/Y, h:i A", strtotime($o["order_date"])) ?>
                    </small>
                </div>

                <span class="status-badge <?= $o['order_status'] ?>">
                    <?= strtoupper($o['order_status']) ?>
                </span>
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
                            Qty: <?= $item["quantity"] ?> × <?= number_format($item["price"],2) ?> ฿
                        </li>
                    <?php endwhile; ?>
                </ul>

                <hr>

                <!-- PRESCRIPTION -->
                <p>
                    <strong>Prescription:</strong><br>
                    <?= nl2br(htmlspecialchars($o["prescription_note"] ?: "-")) ?>
                </p>

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

                <hr>

                <strong>Total Amount:</strong>
                <?= number_format($o["total_amount"], 2) ?> ฿

            </div>
        </div>
        <?php endwhile; ?>

    </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#from_date", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "F j, Y"
});

flatpickr("#to_date", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "F j, Y"
});
</script>
<script src="../assets/js/darkmode.js"></script>
</body>
</html>