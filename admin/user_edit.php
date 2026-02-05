<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: users.php");
    exit;
}

$user_id  = $_POST["user_id"];
$username = trim($_POST["username"]);
$password = trim($_POST["password"]);
$role     = $_POST["role"];

if (empty($user_id) || empty($username) || empty($role)) {
    header("Location: users.php?error=missing_fields");
    exit;
}

// Check for duplicate username (exclude current user)
$check = $conn->prepare("SELECT user_id FROM users WHERE username = ? LIMIT 1");
$check->bind_param("s", $username);
$check->execute();
$existing = $check->get_result()->fetch_assoc();
$check->close();

if ($existing && (int)$existing['user_id'] !== (int)$user_id) {
    header("Location: users.php?error=username_taken");
    exit;
}

// Update username and optionally password
if ($password === "") {
    // Keep current password
    $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $username, $role, $user_id);
} else {
    // Update password too
    $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $username, $password, $role, $user_id);
}

if ($stmt->execute()) {
    header("Location: users.php?success=user_updated");
} else {
    header("Location: users.php?error=update_failed");
}

$stmt->close();
$conn->close();
exit;

?>
