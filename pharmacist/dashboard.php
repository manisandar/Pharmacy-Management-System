<?php
$required_role = "pharmacist";
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$pharmacist_id = $_SESSION["user_id"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pharmacist | Pending Orders</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/edit-profile-btn.css">
</head>
<body>

<div class="dashboard-page">

<?php include "../includes/header.php"; ?>

<?php include "../includes/alerts.php"; ?>

<div class="dashboard-container">

    <!-- ===== STATS ===== -->
    <?php
    $newOrders = $conn->query(
        "SELECT COUNT(*) total 
        FROM orders 
        WHERE order_status='pending'"
    )->fetch_assoc()["total"];
    
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
        "SELECT COUNT(*) total FROM orders
         WHERE order_status='completed'
         AND DATE(order_date)=CURDATE()"
    )->fetch_assoc()["total"];
    ?>

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
            <a class="active" href="#">Pending</a>
            <a href="orders_approved.php">Approved</a>
            <a href="order_history.php">History</a>
        </div>

        <h3 style="margin:20px 0;">Pending Orders</h3>

        <?php
        $orders = $conn->query(
            "SELECT o.*, c.customer_name, c.contact_number
             FROM orders o
             JOIN customers c ON o.customer_id = c.customer_id
             WHERE o.order_status='pending'
             ORDER BY o.order_date ASC"
        );

        if ($orders->num_rows === 0):
        ?>
            <p style="color:#777;">No pending orders.</p>
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

                <span class="status-badge pending">PENDING</span>
            </div>

            <div class="pending-body">
                <strong>Order ID:</strong> <?= $o["order_id"] ?>

                <div class="medicine-list-box">
                    <strong>Medicines:</strong>

                    <?php
                    $items = $conn->query(
                        "SELECT oi.*, m.medicine_name
                         FROM order_items oi
                         JOIN medicines m ON oi.medicine_id = m.medicine_id
                         WHERE oi.order_id = {$o['order_id']}"
                    );
                    ?>

                    <?php while ($item = $items->fetch_assoc()): ?>
                        <div class="medicine-row">
                            <div>
                                <strong><?= $item["medicine_name"] ?></strong><br>
                                Qty: <?= $item["quantity"] ?> × <?= $item["price"] ?>฿
                            </div>
                            <!-- removed inline form; modifications use (order_id, medicine_id) pair -->
                        </div>
                    <?php endwhile; ?>
                </div>

                <?php
                $medicinesArr = [];

                $items = $conn->query(
                    "SELECT 
                        oi.medicine_id,
                        oi.quantity,
                        oi.price,
                        m.medicine_name
                    FROM order_items oi
                    JOIN medicines m ON oi.medicine_id = m.medicine_id
                    WHERE oi.order_id = {$o['order_id']}"
                );

                while ($i = $items->fetch_assoc()) {
                    $medicinesArr[] = [
                        "medicine_id" => $i["medicine_id"],
                        "name" => $i["medicine_name"],
                        "qty" => $i["quantity"],
                        "price" => $i["price"]
                    ];
                }

                $orderData = [
                    "order_id" => $o["order_id"],
                    "customer_name" => $o["customer_name"],
                    "prescription" => $o["prescription_note"], // or prescription column if exists
                    "allergy_notes" => "",
                    "medicines" => $medicinesArr
                ];
                ?>

                <button class="btn-success"
                        onclick='openProcessModal(<?= json_encode($orderData) ?>)'>
                    Process Order
                </button>

            </div>
        </div>
        <?php endwhile; ?>

    </div>
</div>
</div>

<!-- PROCESS ORDER MODAL -->
<!-- EDIT PROFILE handled in header for staff/pharmacist -->
<div id="processOrderModal" class="modal-overlay" style="display:none;">
    <div class="modal-box large">

        <div class="modal-header">
            <h3>Process Order</h3>
            <button class="modal-close" onclick="closeProcessModal()">×</button>
        </div>

        <form method="POST" action="process_order_action.php">

            <!-- Hidden Order ID -->
            <input type="hidden" name="order_id" id="process_order_id">

            <!-- CUSTOMER INFO -->
            <div class="info-box">
                <p><strong>Name:</strong> <span id="p_customer_name"></span></p>
                <p><strong>Prescription:</strong> <span id="p_prescription"></span></p>
            </div>

            <!-- MEDICINES -->
            <h4>Medicines</h4>
            <div id="processMedicineList">
                <!-- Filled by JS -->
            </div>

            <!-- ALLERGY CHECK -->
            <div class="form-group full">
                <label>Allergy Check</label>
                <textarea name="allergy_notes"
                          id="p_allergy_notes"
                          placeholder="Document any allergies..."></textarea>
            </div>

            <!-- PHARMACIST INSTRUCTIONS -->
            <div class="form-group full">
                <label>Instruction & Notes</label>
                <textarea name="pharmacist_instructions"
                          placeholder="How to take medicine, frequency..."></textarea>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="modal-actions">
                <button type="submit"
                        name="action"
                        value="approve"
                        class="btn-primary">
                    Approve
                </button>

                <button type="submit"
                        name="action"
                        value="reject"
                        class="btn-danger">
                    Reject
                </button>
            </div>

        </form>
    </div>
</div>


<script>
let removedItems = [];
function openProcessModal(order) {
    document.getElementById("processOrderModal").style.display = "flex";

    document.getElementById("process_order_id").value = order.order_id;
    document.getElementById("p_customer_name").innerText = order.customer_name;
    document.getElementById("p_prescription").innerText = order.prescription || "-";
    document.getElementById("p_allergy_notes").value = "";
    removedItems = [];
    const list = document.getElementById("processMedicineList");
    list.innerHTML = "";

    order.medicines.forEach(m => {
        list.innerHTML += `
            <div class="medicine-row" data-id="${m.medicine_id}">
                <div>
                    <strong>${m.name}</strong><br>
                    <small>Tablet — Qty: ${m.qty} × ${m.price}฿</small>
                </div>

                <button type="button"
                        class="btn-remove"
                        onclick="removeMedicineFromOrder(${m.medicine_id})">
                    Remove
                </button>
            </div>
        `;
    });
}

function closeProcessModal() {
    document.getElementById("processOrderModal").style.display = "none";
}

function removeMedicineFromOrder(orderItemId) {

    // 1️⃣ Add to removed list
    if (!removedItems.includes(orderItemId)) {
        removedItems.push(orderItemId);
    }

    // 2️⃣ Remove from UI
    const row = document.querySelector(
        `.medicine-row[data-id="${orderItemId}"]`
    );
    if (row) row.remove();

    // 3️⃣ Sync hidden inputs
    syncRemovedItems();
}

function syncRemovedItems() {
    // Remove old inputs
    document
        .querySelectorAll("input[name='removed_items[]']")
        .forEach(i => i.remove());

    // Add only removed ones
    const form = document.querySelector("#processOrderModal form");

    removedItems.forEach(id => {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "removed_items[]";
        input.value = id;
        form.appendChild(input);
    });
}




</script>

<script src="../assets/js/darkmode.js"></script>
</body>
</html>
