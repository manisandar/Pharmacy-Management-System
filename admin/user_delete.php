<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

if (!isset($_GET["id"])) {
    header("Location: users.php");
    exit;
}

$user_id = $_GET["id"];

$stmt = $conn->prepare(
    "DELETE FROM users WHERE user_id = ?"
);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    header("Location: users.php?success=user_deleted");
} else {
    header("Location: users.php?error=delete_failed");
}

$stmt->close();
$conn->close();
exit;
