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
    <title>Pending Orders | PharmFlow</title>
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
        <?php if (isset($_GET["success"])): ?>
            <p style="color:floralwhite; 
                        margin:10px 0;"
                        >
                        Order created successfully.</p>
        <?php endif; ?>

        <?php if (isset($_GET["error"]) && $_GET["error"] === "stock"): ?>
            <p style="color:red; margin:10px 0;">Not enough stock.</p>
        <?php endif; ?>

        <!-- PENDING ORDERS PANEL -->
        <div class="panel" style="margin-top:20px;">

            <div class="tabs">
                <a href="dashboard.php">Customers</a>
                <a class="active" href="#">Pending</a>
                <a href="orders_ready.php">Ready For Payment</a>
                <a href="order_history.php">History</a>
            </div>

            <div class="panel-header">
                <h3>Pending Orders</h3>
                <button class="btn-primary" onclick="openOrderModal()">
                    + New Order
                </button>
            </div>

            <!-- ORDER LIST -->
            <?php
            $orders = $conn->query(
                "SELECT 
                    o.order_id,
                    o.order_date,
                    c.customer_name,
                    c.contact_number,
                    c.customer_id
                FROM orders o
                JOIN customers c ON o.customer_id = c.customer_id
                WHERE o.order_status = 'pending'
                ORDER BY o.order_date DESC"
            );


            if ($orders->num_rows === 0):
            ?>
                <p style="color:#777; padding:20px;">No pending orders.</p>
            <?php
            endif;

            while ($o = $orders->fetch_assoc()):
            ?>
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
                    <strong>Order ID:</strong> <?= $o["order_id"] ?><br><br>
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

                        $totalAmount = 0;
                        while ($item = $items->fetch_assoc()):
                            $totalAmount += $item["quantity"] * $item["price"];
                        ?>
                            <li>
                                <strong><?= htmlspecialchars($item["medicine_name"]) ?></strong><br>
                                Tablet — Qty: <?= $item["quantity"] ?> × <?= $item["price"] ?>฿
                            </li>
                        <?php endwhile; ?>
                    </ul>
                    <div class="order-total">
                        Total Amount: <strong>฿ <?= number_format($totalAmount, 2) ?></strong>
                    </div>

                </div>

            </div>
        <?php endwhile; ?>

        </div>
    </div>
</div>

<!-- NEW ORDER MODAL (INLINE) -->
<div id="orderModal" class="modal-overlay" style="display:none;">
    <div class="modal-box large">

        <div class="modal-header">
            <h3>Create New Order</h3>
            <button class="modal-close" onclick="closeOrderModal()">×</button>
        </div>

        <form method="POST" action="create_order.php">

    <!-- CUSTOMER -->
    <div class="form-row">
        <div class="form-group" style="position: relative;">
            <label>Patient Name *</label>
            <input type="text"
                   id="customer_name"
                   name="customer_name"
                   placeholder="Enter patient name"
                   autocomplete="off"
                   required>
            <div id="customerDropdown" class="dropdown-list"></div>
        </div>

        <div class="form-group">
            <label>Patient ID</label>
            <input type="text" id="customer_id_display" readonly>
            <input type="hidden" name="customer_id" id="customer_id">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group full">
            <label>Contact Number *</label>
            <input type="text" id="contact_number" name="customer_name" readonly required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group full">
            <label>Prescription Notes</label>
            <textarea name="prescription_note"
                      placeholder="Doctor’s prescription details..."></textarea>
        </div>
    </div>
    <hr>

<div class="form-row">
    <div class="form-group">
        <label>Select Medicines</label>
        <select id="medicineSelect">
            <option value="">Select Medicine</option>
            <?php
            $meds = $conn->query(
                "SELECT medicine_id, medicine_name, price_per_unit, quantity
                 FROM medicines
                 ORDER BY medicine_name ASC"
            );
            while ($m = $meds->fetch_assoc()) {
                echo "<option value='{$m['medicine_id']}'
                             data-price='{$m['price_per_unit']}'
                             data-stock='{$m['quantity']}'>
                        {$m['medicine_name']}
                      </option>";
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label>Amount</label>
        <input type="number" id="medicineQty" value="1" min="1">
    </div>
</div>

<button type="button" class="btn-success" onclick="addMedicine()">
    Add
</button>
<div id="medicineList"></div>

<div id="orderTotal" class="order-total">
    Total: ฿ 0.00
</div>

<input type="hidden" name="order_items" id="orderItemsInput">

<button type="submit" class="btn-primary full-width">
    Create Order
</button>
</form>


    </div>
</div>

<script>
function openOrderModal() {
    document.getElementById("orderModal").style.display = "flex";
}
function closeOrderModal() {
    document.getElementById("orderModal").style.display = "none";
}

/* ===== CUSTOMER AUTOCOMPLETE ===== */
const nameInput = document.getElementById("customer_name");
const dropdown = document.getElementById("customerDropdown");

nameInput.addEventListener("keyup", async function () {
    const q = this.value.trim();
    if (q.length < 2) {
        dropdown.style.display = "none";
        return;
    }

    const res = await fetch(`search_customers.php?q=${encodeURIComponent(q)}`);
    const data = await res.json();

    dropdown.innerHTML = "";
    if (data.length === 0) {
        dropdown.style.display = "none";
        return;
    }

    data.forEach(c => {
        const div = document.createElement("div");
        div.className = "dropdown-item";
        div.innerHTML = `<strong>${c.customer_name}</strong><br>${c.contact_number}`;

        div.onclick = () => {
            nameInput.value = c.customer_name;
            document.getElementById("customer_id").value = c.customer_id;
            document.getElementById("customer_id_display").value = c.customer_id;
            document.getElementById("contact_number").value = c.contact_number;
            dropdown.style.display = "none";
        };

        dropdown.appendChild(div);
    });

    dropdown.style.display = "block";
});

/* ===== MEDICINE LOGIC ===== */
let orderItems = [];
let totalAmount = 0;

function addMedicine() {
    const select = document.getElementById("medicineSelect");
    const qtyInput = document.getElementById("medicineQty");

    if (!select.value) {
        alert("Select medicine");
        return;
    }

    const qty = parseInt(qtyInput.value);
    const option = select.options[select.selectedIndex];
    const price = parseFloat(option.dataset.price);
    const stock = parseInt(option.dataset.stock);

    if (qty > stock) {
        alert("Not enough stock");
        return;
    }

    orderItems.push({
        id: select.value,
        name: option.text,
        qty,
        price,
        subtotal: qty * price
    });

    renderMedicines();
}

function removeMedicine(i) {
    orderItems.splice(i, 1);
    renderMedicines();
}

function renderMedicines() {
    const list = document.getElementById("medicineList");
    list.innerHTML = "";
    totalAmount = 0;

    orderItems.forEach((item, i) => {
        totalAmount += item.subtotal;
        list.innerHTML += `
            <div class="medicine-item">
                <div>
                    <strong>${item.name}</strong><br>
                    Qty: ${item.qty} × ${item.price}
                </div>
                <button type="button" class="btn-delete"
                        onclick="removeMedicine(${i})">
                    Remove
                </button>
            </div>
        `;
    });

    document.getElementById("orderTotal").innerText =
        "Total: ฿ " + totalAmount.toFixed(2);

    document.getElementById("orderItemsInput").value =
        JSON.stringify(orderItems);
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
