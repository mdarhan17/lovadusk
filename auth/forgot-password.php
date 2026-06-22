<?php
$pageTitle = "Forgot Password | LOVA DUSK";
require_once "../config/constants.php";
require_once "../config/db.php";
require_once "../includes/functions.php";
require_once "../includes/csrf.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $email = strtolower(trim($_POST["email"] ?? ""));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email.";
    } else {
        $message = "If this email exists, password reset instructions will be shared. For demo use, contact admin.";
    }
}

include "../includes/header.php";
?>

<section class="auth-section">
    <div class="auth-card">
        <p class="eyebrow">Account Help</p>
        <h1>Forgot Password</h1>

        <?php if ($message): ?>
            <div class="alert"><?= clean($message); ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <?= csrf_input(); ?>
            <label>Email</label>
            <input type="email" name="email" required>
            <button type="submit" class="btn btn-dark">Submit</button>
        </form>

        <p class="auth-note"><a href="login.php">Back to login</a></p>
    </div>
</section>

<?php include "../includes/footer.php"; ?>