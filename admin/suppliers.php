<?php
$required_role = "admin";
require_once "../includes/auth_check.php";
require_once "../config/db.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier Management | PharmFlow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<div class="dashboard-page">

    <!-- HEADER -->
    <?php include "../includes/header.php"; ?>
    
    <div class="dashboard-container">

    <!-- STATS (optional, keep if you want same as dashboard) -->
    <?php include "../includes/stats_supplier.php"; ?>


        <!-- ALERTS -->
        <?php include "../includes/alerts.php"; ?>

        <div class="panel">

            <!-- TABS -->
            <div class="tabs">
                <a href="sales_report.php">Sale Report</a>
                <a href="dashboard.php">Stock Management</a>
                <a href="users.php">User Management</a>
                <a class="active" href="suppliers.php">Suppliers</a>
            </div>

            <div class="panel-header">
                <h3>Suppliers</h3>
                <button class="btn-primary" onclick="openAddSupplier()">+ Add Supplier</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Supplier Name</th>
                        <th>Contact Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                <?php
                $result = $conn->query("SELECT * FROM suppliers ORDER BY supplier_id DESC");
                while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row["supplier_name"]) ?></td>
                        <td><?= htmlspecialchars($row["contact_number"]) ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-edit"
                                    onclick='openEditSupplier(<?= json_encode($row) ?>)'>
                                    Edit
                                </button>

                                <a class="btn-delete"
                                   href="supplier_delete.php?id=<?= $row["supplier_id"] ?>"
                                   onclick="return confirm('Delete this supplier?')">
                                   Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>

                </tbody>
            </table>

        </div>
    </div>
</div>

<!-- MODALS -->
<?php include "../includes/supplier_modals.php"; ?>

<script src="../assets/js/suppliers.js"></script>
<script>
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
