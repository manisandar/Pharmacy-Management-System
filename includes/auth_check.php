<?php
session_start();

// If user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: /phpapp/index.php");
    exit;
}

// Optional: role-based protection
// Usage: $required_role = 'admin'; (set in page)
if (isset($required_role)) {
    if ($_SESSION["role"] !== $required_role) {
        header("Location: /phpapp/index.php");
        exit;
    }
}
?>
