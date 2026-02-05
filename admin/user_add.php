<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: users.php");
    exit;
}

$username = trim($_POST["username"]);
$password = trim($_POST["password"]);
$role     = $_POST["role"];

if (empty($username) || empty($password) || empty($role)) {
    header("Location: users.php?error=missing_fields");
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO users (username, password, role) VALUES (?, ?, ?)"
);
$stmt->bind_param("sss", $username, $password, $role);

if ($stmt->execute()) {
    header("Location: users.php?success=user_added");
} else {
    header("Location: users.php?error=insert_failed");
}

$stmt->close();
$conn->close();
exit;
 