<?php
$required_role = "admin";
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$statusFilter = $_GET["status"] ?? "all";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | PharmFlow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
<div class="dashboard-page">
    <?php include "../includes/header.php"; ?>

    <!-- DASHBOARD CONTENT -->
    <div class="dashboard-container">

        <!-- STATS CARDS -->
        <?php include "../includes/stats_stock.php"; ?>
        <?php include "../includes/alerts.php"; ?>
        <!-- MAIN PANEL -->
        <div class="panel">
                <!-- NAV TABS -->
            <div class="tabs">
                <a href="sales_report.php">Sale Report</a>
                <a class="active" href="dashboard.php">Stock Management</a>
                <a href="users.php">User Management</a>
                <a href="suppliers.php">Suppliers</a>
            </div>

            <div class="panel-header">
                <!-- STOCK STATUS TOGGLE -->
                <div class="role-toggle">
                    <a class="<?= $statusFilter === 'all' ? 'active' : '' ?>"
                    href="dashboard.php?status=all">All</a>

                    <a class="<?= $statusFilter === 'instock' ? 'active' : '' ?>"
                    href="dashboard.php?status=instock">In Stock</a>

                    <a class="<?= $statusFilter === 'lowstock' ? 'active' : '' ?>"
                    href="dashboard.php?status=lowstock">Low Stock</a>

                    <a class="<?= $statusFilter === 'expired' ? 'active' : '' ?>"
                    href="dashboard.php?status=expired">Expired</a>

                    <a class="<?= $statusFilter === 'expiresoon' ? 'active' : '' ?>"
                    href="dashboard.php?status=expiresoon">Expire Soon</a>
                </div>
                <button class="btn-primary" onclick="openModal()">+ Add Medicine</button>

            </div>

            <!-- TABLE (STATIC FOR NOW) -->
            <table>
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Medicine ID</th>
                        <th>Form</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Supplier</th>
                        <th>Expiry</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $sql = "
                    SELECT m.*, s.supplier_name
                    FROM medicines m
                    LEFT JOIN suppliers s ON m.supplier_id = s.supplier_id
                ";

                $today = date("Y-m-d");
                $soon  = date("Y-m-d", strtotime("+60 days"));

                switch ($statusFilter) {

                    case "expired":
                        $sql .= "
                            WHERE m.expiry_date < '$today'
                        ";
                        break;

                    case "expiresoon":
                        $sql .= "
                            WHERE m.expiry_date >= '$today'
                            AND m.expiry_date <= '$soon'
                        ";
                        break;

                    case "lowstock":
                        $sql .= "
                            WHERE m.quantity <= m.reorder_level
                            AND m.expiry_date > '$soon'
                        ";
                        break;

                    case "instock":
                        $sql .= "
                            WHERE m.quantity > m.reorder_level
                            AND m.expiry_date > '$soon'
                        ";
                        break;

                    default:
                        // all
                        break;
                }

                $sql .= " ORDER BY m.medicine_name ASC";

                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()):
                    
                    // Status logic
                    $status = "In Stock";
                    $statusClass = "instock";
                    $today = date("Y-m-d");
                    $soon  = date("Y-m-d", strtotime("+60 days"));

                    if ($row["expiry_date"] < $today) {
                        $status = "Expired";
                        $statusClass = "expired";
                    }
                    elseif ($row["expiry_date"] <= $soon) {
                        $status = "Expire Soon";
                        $statusClass = "expiresoon";
                    }
                    elseif ($row["quantity"] <= $row["reorder_level"]) {
                        $status = "Low Stock";
                        $statusClass = "lowstock";
                    }
                    else {
                        $status = "In Stock";
                        $statusClass = "instock";
                    }
                ?>
                <tr>
                    <td><?= htmlspecialchars($row["medicine_name"]) ?></td>
                    <td><?= $row["medicine_id"] ?></td>
                    <td><?= htmlspecialchars($row["dosage_form"]) ?></td>
                    <td><?= $row["quantity"] ?></td>
                    <td><?= $row["price_per_unit"] ?>฿</td>
                    <td><?= htmlspecialchars($row["supplier_name"]) ?></td>
                    <td><?= date("m/y", strtotime($row["expiry_date"])) ?></td>
                    <td class="status <?= $statusClass ?>">
                        <?= $status ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button
                            class="btn-edit"
                            onclick='openEditModal(<?= json_encode($row) ?>)'>
                            Edit
                            </button>
                            <a 
                                href="medicine_delete.php?id=<?= $row['medicine_id'] ?>" 
                                class="btn-delete"
                                onclick="return confirm('Are you sure you want to delete this medicine?')">
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


<!-- MODAL OVERLAY -->
<div id="modalOverlay" class="modal-overlay">

    <!-- MODAL BOX -->
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add Medicine</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>

        <div class="modal-body">
            <!-- ADD MEDICINE FORM -->
            <form method="POST" action="medicines_add.php">

                <div class="form-row">
                    <div class="form-group">
                        <label>Medicine Name *</label>
                        <input type="text" name="medicine_name" required>
                    </div>

                    <div class="form-group">
                        <label>Chemical Name</label>
                        <input type="text" name="chemical_name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Dosage Form *</label>
                        <select name="dosage_form" required>
                            <option value="">Select</option>
                            <option>Tablet</option>
                            <option>Injection (Vial/Ampoule)</option>
                            <option>Syrup</option>
                            <option>Gel/Cream/Ointment</option>
                            <option>Drops (Eye/Ear/Nasal)</option>
                            <option>Suppository</option>
                            <option>Inhaler</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Price Per Unit *</label>
                        <input type="number" step="0.01" name="price_per_unit" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Quantity *</label>
                        <input type="number" name="quantity" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Reorder Level *</label>
                        <input type="number" name="reorder_level" min="0" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Expiry Date *</label>
                        <input type="date" name="expiry_date" required>
                    </div>

                    <div class="form-group">
                        <label>Supplier *</label>
                        <select name="supplier_id" required>
                            <option value="">Select Supplier</option>
                            <?php
                            $supplierResult = $conn->query("SELECT supplier_id, supplier_name FROM suppliers");

                            while ($supplier = $supplierResult->fetch_assoc()) {
                                echo "<option value='{$supplier['supplier_id']}'>
                                        {$supplier['supplier_name']}
                                    </option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-primary full-width">
                    Save Medicine
                </button>

            </form>
        </div>
    </div>
</div>
<!-- EDIT MEDICINE MODAL -->
<div id="editModalOverlay" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit Medicine</h3>
            <button class="modal-close" onclick="closeEditModal()">×</button>
        </div>

        <div class="modal-body">
            <form method="POST" action="medicine_edit.php">

                <input type="hidden" name="medicine_id" id="edit_medicine_id">

                <div class="form-row">
                    <div class="form-group">
                        <label>Medicine Name *</label>
                        <input type="text" name="medicine_name" id="edit_medicine_name" required>
                    </div>

                    <div class="form-group">
                        <label>Chemical Name</label>
                        <input type="text" name="chemical_name" id="edit_chemical_name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Dosage Form *</label>
                        <select name="dosage_form" id="edit_dosage_form" required>
                            
                            <option>Tablet</option>
                            <option>Injection (Vial/Ampoule)</option>
                            <option>Syrup</option>
                            <option>Gel/Cream/Ointment</option>
                            <option>Drops (Eye/Ear/Nasal)</option>
                            <option>Suppository</option>
                            <option>Inhaler</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Price Per Unit *</label>
                        <input type="number" step="0.01" name="price_per_unit" id="edit_price" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Quantity *</label>
                        <input type="number" name="quantity" id="edit_quantity" required>
                    </div>

                    <div class="form-group">
                        <label>Reorder Level *</label>
                        <input type="number" name="reorder_level" id="edit_reorder" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Expiry Date *</label>
                        <input type="date" name="expiry_date" id="edit_expiry" required>
                    </div>

                    <div class="form-group">
                        <label>Supplier *</label>
                        <select name="supplier_id" id="edit_supplier" required>
                            <?php
                            $supplierResult = $conn->query("SELECT supplier_id, supplier_name FROM suppliers");
                            while ($s = $supplierResult->fetch_assoc()) {
                                echo "<option value='{$s['supplier_id']}'>{$s['supplier_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-primary full-width">
                    Update Medicine
                </button>

            </form>
        </div>
    </div>
</div>

<script src="../assets/js/dashboard.js"></script>


</body>
</html>
