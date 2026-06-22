<?php
require_once "config/constants.php";
require_once "config/db.php";
require_once "includes/functions.php";
require_once "includes/csrf.php";
require_once "database/phase9-schema.php";

ensure_phase9_schema($conn);

$pageTitle = "Contact | LOVA DUSK";
$messageText = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $name = trim($_POST["name"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $phone = trim($_POST["phone"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if ($name === "" || $email === "" || $message === "") {
        $messageText = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messageText = "Please enter a valid email.";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
        $stmt->execute();

        $messageText = "Your message has been sent successfully.";
    }
}

include "includes/header.php";
?>

<section class="page-hero">
    <p class="eyebrow">Get In Touch</p>
    <h1>Contact LOVA DUSK</h1>
    <p>For orders, drops, collaborations and support.</p>
</section>

<section class="auth-section">
    <div class="auth-card contact-card">
        <?php if ($messageText): ?>
            <div class="alert"><?= clean($messageText); ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <?= csrf_input(); ?>

            <label>Name</label>
            <input type="text" name="name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Phone</label>
            <input type="text" name="phone">

            <label>Subject</label>
            <input type="text" name="subject">

            <label>Message</label>
            <textarea name="message" required></textarea>

            <button type="submit" class="btn btn-dark">Send Message</button>
        </form>
    </div>
</section>

<?php include "includes/footer.php"; ?>