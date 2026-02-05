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
     WHERE staff_id = $staff_id
       AND order_status = 'completed'
       AND DATE(order_date) = CURDATE()"
)->fetch_assoc()["total"] ?? 0;

$rejectedCount = $conn->query(
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE order_status = 'rejected'"
)->fetch_assoc()["total"] ?? 0;


/* ===== CREATE CUSTOMER (MODAL SUBMIT) ===== */
$customer_error = "";
$customer_success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_customer"])) {

    $name = trim($_POST["customer_name"]);
    $phone = trim($_POST["contact_number"]);
    $address = trim($_POST["address"]);

    if ($name === "" || $phone === "") {
        $customer_error = "Customer name and contact number are required.";
    } else {
        // Reject duplicate (name + contact + address)
        $stmt = $conn->prepare(
            "SELECT customer_id FROM customers
             WHERE customer_name = ?
               AND contact_number = ?
               AND address = ?"
        );
        $stmt->bind_param("sss", $name, $phone, $address);
        $stmt->execute();
        $exist = $stmt->get_result()->fetch_assoc();

        if ($exist) {
            $customer_error = "This customer already exists.";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO customers (customer_name, contact_number, address)
                 VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $name, $phone, $address);
            $stmt->execute();

            $customer_success = "Customer added successfully.";
        }
    }
}

/* ===== UPDATE CUSTOMER ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit_customer"])) {

    $id = (int)$_POST["customer_id"];
    $name = trim($_POST["customer_name"]);
    $phone = trim($_POST["contact_number"]);
    $address = trim($_POST["address"]);

    if ($name !== "" && $phone !== "") {
        $stmt = $conn->prepare(
            "UPDATE customers
             SET customer_name = ?, contact_number = ?, address = ?
             WHERE customer_id = ?"
        );
        $stmt->bind_param("sssi", $name, $phone, $address, $id);
        $stmt->execute();
    }
}

/* ===== DELETE CUSTOMER ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_customer"])) {

    $id = (int)$_POST["customer_id"];

    $stmt = $conn->prepare(
        "DELETE FROM customers WHERE customer_id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard | PharmFlow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/edit-profile-btn.css">
</head>
<body>

<div class="dashboard-page">

    <!-- HEADER -->
    <?php include "../includes/header.php"; ?>

    <?php include "../includes/alerts.php"; ?>

    <div class="dashboard-container">

        <!-- STAFF STATS -->
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

        <!-- CUSTOMERS PANEL -->
        <div class="panel" style="margin-top:20px;">

            <div class="tabs">
                <a class="active" href="#">Customers</a>
                <a href="pending_orders.php">Pending</a>
                <a href="orders_ready.php">Ready For Payment</a>
                <a href="order_history.php">History</a>
            </div>

            <div class="panel-header">
                <h3>Customers</h3>
                <button class="btn-primary" onclick="openModal()">+ Add Customer</button>
            </div>

            <!-- SUCCESS / ERROR MESSAGE -->
            <?php if ($customer_error): ?>
                <p style="color:red; margin:10px 0;">
                    <?= htmlspecialchars($customer_error) ?>
                </p>
            <?php endif; ?>

            <?php if ($customer_success): ?>
                <p style="color:green; margin:10px 0;">
                    <?= htmlspecialchars($customer_success) ?>
                </p>
            <?php endif; ?>

            <!-- CUSTOMERS TABLE -->
            <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th style="width:140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $customers = $conn->query(
                "SELECT customer_id, customer_name, contact_number, address
                FROM customers
                ORDER BY customer_id DESC"
            );

            while ($c = $customers->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $c["customer_id"] ?></td>
                    <td><?= htmlspecialchars($c["customer_name"]) ?></td>
                    <td><?= htmlspecialchars($c["contact_number"]) ?></td>
                    <td><?= htmlspecialchars($c["address"]) ?></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-small btn-edit"
                                onclick="openEditModal(
                                    <?= $c['customer_id'] ?>,
                                    '<?= htmlspecialchars(addslashes($c['customer_name'])) ?>',
                                    '<?= htmlspecialchars(addslashes($c['contact_number'])) ?>',
                                    '<?= htmlspecialchars(addslashes($c['address'])) ?>'
                                )">
                                Edit
                            </button>

                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="customer_id" value="<?= $c['customer_id'] ?>">
                                <button type="submit"
                                    name="delete_customer"
                                    class="btn-small btn-delete"
                                    onclick="return confirm('Delete this customer?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- ADD CUSTOMER MODAL -->
<div id="customerModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">

        <div class="modal-header">
            <h3>Add Customer</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>

        <form method="POST">
            <input type="hidden" name="add_customer" value="1">

            <div class="form-row">
                <div class="form-group full">
                    <label>Customer Name *</label>
                    <input type="text" name="customer_name" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full">
                    <label>Contact Number *</label>
                    <input type="text" name="contact_number" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full">
                    <label>Address</label>
                    <textarea name="address"></textarea>
                </div>
            </div>

            <button type="submit" class="btn-primary full-width">
                Save Customer
            </button>
        </form>
    </div>
</div>

<div id="editCustomerModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">

        <div class="modal-header">
            <h3>Edit Customer</h3>
            <button class="modal-close" onclick="closeEditModal()">×</button>
        </div>

        <form method="POST">
            <input type="hidden" name="edit_customer" value="1">
            <input type="hidden" name="customer_id" id="edit_customer_id">

            <div class="form-row">
                <div class="form-group full">
                    <label>Customer Name *</label>
                    <input type="text" name="customer_name" id="edit_name" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full">
                    <label>Contact Number *</label>
                    <input type="text" name="contact_number" id="edit_phone" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full">
                    <label>Address</label>
                    <textarea name="address" id="edit_address"></textarea>
                </div>
            </div>

            <button type="submit" class="btn-primary full-width">
                Update Customer
            </button>
        </form>
    </div>
</div>


<script>
function openModal() {
    document.getElementById("customerModal").style.display = "flex";
}
function closeModal() {
    document.getElementById("customerModal").style.display = "none";
}

function openEditModal(id, name, phone, address) {
    document.getElementById("edit_customer_id").value = id;
    document.getElementById("edit_name").value = name;
    document.getElementById("edit_phone").value = phone;
    document.getElementById("edit_address").value = address;

    document.getElementById("editCustomerModal").style.display = "flex";
}

function closeEditModal() {
    document.getElementById("editCustomerModal").style.display = "none";
}


</script>

<?php if ($customer_success): ?>
<script>
    closeModal();
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
<?php endif; ?>
<script src="../assets/js/darkmode.js"></script>
</body>
</html>
