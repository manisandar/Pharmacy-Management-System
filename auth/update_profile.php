<?php
require_once __DIR__ . "/../includes/auth_check.php";
require_once __DIR__ . "/../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit;
}

$user_id = intval($_POST["user_id"] ?? 0);
$username = trim($_POST["username"] ?? "");
$password = trim($_POST["password"] ?? "");

if ($user_id <= 0 || $username === "") {
    header("Location: ../index.php?error=missing_fields");
    exit;
}

// Check for duplicate username (exclude current user)
$check = $conn->prepare("SELECT user_id FROM users WHERE username = ? LIMIT 1");
$check->bind_param("s", $username);
$check->execute();
$existing = $check->get_result()->fetch_assoc();
$check->close();

if ($existing && (int)$existing['user_id'] !== $user_id) {
    // Username taken — redirect back to dashboard with specific error
    $conn->close();
    $redirect = isset($_SESSION['role']) ? $_SESSION['role'] . "/dashboard.php" : "index.php";
    header("Location: ../$redirect?error=username_taken");
    exit;
}

// Update username and optionally password
if ($password === "") {
    $stmt = $conn->prepare("UPDATE users SET username = ? WHERE user_id = ?");
    $stmt->bind_param("si", $username, $user_id);
} else {
    // NOTE: currently passwords are stored plain in this app; consider migrating to password_hash
    $stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $username, $password, $user_id);
}

if ($stmt->execute()) {
    // If current user updated, refresh session username
    if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $user_id) {
        $_SESSION["username"] = $username;
    }
    $stmt->close();
    $conn->close();
    header("Location: ../" . (isset($_SESSION['role']) ? $_SESSION['role'] . "/dashboard.php" : "index.php") . "?success=profile_updated");
    exit;
} else {
    $stmt->close();
    $conn->close();
    header("Location: ../index.php?error=update_failed");
    exit;
}

?>
