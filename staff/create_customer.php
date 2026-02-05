<?php
$required_role = "staff";
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["customer_name"]);
    $phone = trim($_POST["contact_number"]);
    $address = trim($_POST["address"]);

    if ($name === "" || $phone === "") {
        $error = "Customer name and contact number are required.";
    } else {

        // 🔒 DUPLICATE CHECK (name + phone + address)
        $stmt = $conn->prepare(
            "SELECT customer_id 
             FROM customers 
             WHERE customer_name = ? 
               AND contact_number = ? 
               AND address = ?"
        );
        $stmt->bind_param("sss", $name, $phone, $address);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        if ($existing) {
            $error = "This customer already exists in the system.";
        } else {
            // ✅ INSERT CUSTOMER
            $stmt = $conn->prepare(
                "INSERT INTO customers (customer_name, contact_number, address)
                 VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $name, $phone, $address);
            $stmt->execute();

            header("Location: customers.php?success=created");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Customer</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<div class="dashboard-page">
<?php include "../includes/header.php"; ?>

<div class="dashboard-container">
    <div class="panel">
        <h3>Add Customer</h3>

        <?php if ($error): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">

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

            <button type="submit" class="btn-primary">
                Save Customer
            </button>

            <a href="customers.php" class="btn-secondary">
                Cancel
            </a>

        </form>
    </div>
</div>
</div>

</body>
</html>
