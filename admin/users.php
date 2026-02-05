<?php
$required_role = "admin";
require_once "../includes/auth_check.php";
require_once "../config/db.php";

/* ROLE FILTER */
$roleFilter = $_GET["role"] ?? "all";

$sql = "SELECT user_id, username, password, role FROM users";
if ($roleFilter !== "all") {
    $stmt = $conn->prepare(
    "SELECT user_id, username, password, role FROM users WHERE role = ?"
    );
    $stmt->bind_param("s", $roleFilter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Management | PharmFlow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<div class="dashboard-page">

<?php include "../includes/header.php"; ?>

<div class="dashboard-container">
    <?php include "../includes/stats_user.php"; ?>

<?php include "../includes/alerts.php"; ?>

<div class="panel">

    <!-- TABS -->
    <div class="tabs">
        <a href="sales_report.php">Sale Report</a>
        <a href="dashboard.php">Stock Management</a>
        <a class="active" href="users.php">User Management</a>
        <a href="suppliers.php">Suppliers</a>
    </div>

    <!-- ROLE TOGGLE -->
    <div class="role-toggle">
        <a class="<?= $roleFilter === 'all' ? 'active' : '' ?>" href="users.php?role=all">All</a>
        <a class="<?= $roleFilter === 'admin' ? 'active' : '' ?>" href="users.php?role=admin">Admin</a>
        <a class="<?= $roleFilter === 'staff' ? 'active' : '' ?>" href="users.php?role=staff">Staff</a>
        <a class="<?= $roleFilter === 'pharmacist' ? 'active' : '' ?>" href="users.php?role=pharmacist">Pharmacist</a>

        <button class="btn-primary add-user-btn" style="float:right" onclick="openAddUser()">
            + Add User
        </button>
    </div>

    <!-- USERS TABLE -->
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Password</th>
                <th>Role</th>
                <th>User ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row["username"]) ?></td>
                <td>••••••••</td>
                <td><?= ucfirst($row["role"]) ?></td>
                <td><?= $row["user_id"] ?></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-edit"
                            onclick='openEditUser(<?= json_encode($row) ?>)'>
                            Edit
                        </button>

                        <a class="btn-delete"
                        href="user_delete.php?id=<?= $row["user_id"] ?>"
                        onclick="return confirm('Delete this user?')">
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
<?php include "../includes/user_modals.php"; ?>
<script src="../assets/js/users.js"></script>
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
