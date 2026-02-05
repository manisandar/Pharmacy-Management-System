<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

$user_id = intval($_POST["user_id"] ?? 0);
$username = trim($_POST["username"] ?? "");
$password = trim($_POST["password"] ?? "");

if ($user_id <= 0 || $username === "") {
    header("Location: dashboard.php?error=missing_fields");
    exit;
}

// If password left blank, don't update it
if ($password === "") {
    $stmt = $conn->prepare("UPDATE users SET username = ? WHERE user_id = ?");
    $stmt->bind_param("si", $username, $user_id);
} else {
    $stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $username, $password, $user_id);
}

if ($stmt->execute()) {
    // Update session username if current user
    if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $user_id) {
        $_SESSION["username"] = $username;
    }
    $stmt->close();
    $conn->close();
    header("Location: dashboard.php?success=profile_updated");
    exit;
} else {
    $stmt->close();
    $conn->close();
    header("Location: dashboard.php?error=update_failed");
    exit;
}

?>
