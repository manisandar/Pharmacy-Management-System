<?php
session_start();
require_once "config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if ($username === "" || $password === "") {
        $error = "Please enter username and password";
    } else {
        $stmt = $conn->prepare(
            "SELECT user_id, username, password, role 
             FROM users 
             WHERE username = ? LIMIT 1"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // TEMP: plain password check
            // Later we will replace with password_hash()
            if ($password === $user["password"]) {

                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"] = $user["role"];

                // Redirect based on role
                if ($user["role"] === "admin") {
                    header("Location: admin/dashboard.php");
                } elseif ($user["role"] === "staff") {
                    header("Location: staff/dashboard.php");
                } elseif ($user["role"] === "pharmacist") {
                    header("Location: pharmacist/dashboard.php");
                }
                exit;

            } else {
                $error = "Invalid username or password";
            }
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PharmFlow Management</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <h1>PharmFlow<br>Pharmacy</h1>
        <p class="subtitle">Login to continue</p>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter username">

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password">

            <button class="login-button" type="submit">Login</button>
        </form>
    </div>
</div>

</body>
</html>
