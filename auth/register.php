<?php
$pageTitle = "Register | LOVA DUSK";
require_once "../config/constants.php";
require_once "../config/db.php";
require_once "../includes/functions.php";
require_once "../includes/csrf.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $name = trim($_POST["name"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($name === "" || $email === "" || $password === "") {
        $message = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email.";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {
            $message = "Email already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $role = "user";

            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone, $hashedPassword, $role);

            if ($stmt->execute()) {
                session_regenerate_id(true);
                $_SESSION["user_id"] = $stmt->insert_id;
                $_SESSION["user_name"] = $name;
                $_SESSION["user_email"] = $email;

                redirect("user/dashboard.php");
            } else {
                $message = "Something went wrong. Please try again.";
            }
        }
    }
}

include "../includes/header.php";
?>

<section class="auth-section">
    <div class="auth-card">
        <p class="eyebrow">Join The Drop</p>
        <h1>Create Account</h1>

        <?php if ($message): ?>
            <div class="alert"><?= clean($message); ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <?= csrf_input(); ?>

            <label>Name</label>
            <input type="text" name="name" placeholder="Enter your name" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your email" required>

            <label>Phone</label>
            <input type="text" name="phone" placeholder="Enter phone number">

            <label>Password</label>
            <input type="password" name="password" placeholder="Create password" required>

            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Confirm password" required>

            <button type="submit" class="btn btn-dark">Register</button>
        </form>

        <p class="auth-note">
            Already have an account?
            <a href="<?= BASE_URL; ?>auth/login.php">Login</a>
        </p>
    </div>
</section>

<?php include "../includes/footer.php"; ?>
