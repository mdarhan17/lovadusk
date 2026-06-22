<?php
$pageTitle = "Login | LOVA DUSK";
require_once "../config/constants.php";
require_once "../config/db.php";
require_once "../includes/functions.php";
require_once "../includes/csrf.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $message = "Please enter email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {
            if ($user["status"] !== "active") {
                $message = "Your account is blocked.";
            } else {
                session_regenerate_id(true);
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];

                redirect("user/dashboard.php");
            }
        } else {
            $message = "Invalid email or password.";
        }
    }
}

include "../includes/header.php";
?>

<section class="auth-section">
    <div class="auth-card">
        <p class="eyebrow">Account Access</p>
        <h1>Login</h1>

        <?php if ($message): ?>
            <div class="alert"><?= clean($message); ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <?= csrf_input(); ?>

            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <button type="submit" class="btn btn-dark">Login</button>
        </form>

        <p class="auth-note">
            New to LOVA DUSK?
            <a href="<?= BASE_URL; ?>auth/register.php">Create account</a>
        </p>
    </div>
</section>

<?php include "../includes/footer.php"; ?>
