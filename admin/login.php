<?php
$pageTitle = "Admin Login | LOVA DUSK";
require_once "../config/constants.php";
require_once "../config/db.php";
require_once "../includes/functions.php";
require_once "../includes/csrf.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($password, $admin["password"])) {
        if ($admin["status"] !== "active") {
            $message = "Admin account is blocked.";
        } else {
            session_regenerate_id(true);
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_name"] = $admin["name"];
            $_SESSION["admin_email"] = $admin["email"];

            redirect("admin/dashboard.php");
        }
    } else {
        $message = "Invalid admin login.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login | LOVA DUSK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>

<section class="admin-login-section">
    <div class="auth-card">
        <p class="eyebrow">Admin Panel</p>
        <h1>LOVA DUSK</h1>

        <?php if ($message): ?>
            <div class="alert"><?= clean($message); ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <?= csrf_input(); ?>

            <label>Email</label>
            <input type="email" name="email" placeholder="admin@lovadusk.com" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>

            <button type="submit" class="btn btn-dark">Login</button>
        </form>

        <p class="auth-note">
            First time setup?
            <a href="<?= BASE_URL; ?>admin/create-admin.php">Create admin</a>
        </p>
    </div>
</section>

</body>
</html>
