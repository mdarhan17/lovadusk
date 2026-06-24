<?php
require_once "../config/constants.php";
require_once "../config/db.php";
require_once "../includes/functions.php";
require_once "../includes/csrf.php";

$pageTitle = "Login | LOVA DUSK";
$error = "";

function auth_column_exists($conn, $table, $column) {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

$passwordColumn = "password";

if (!auth_column_exists($conn, "users", "password") && auth_column_exists($conn, "users", "password_hash")) {
    $passwordColumn = "password_hash";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Please enter email and password.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) {
            $error = "Invalid email or password.";
        } elseif (isset($user["status"]) && $user["status"] !== "active") {
            $error = "Your account is inactive.";
        } elseif (!password_verify($password, $user[$passwordColumn])) {
            $error = "Invalid email or password.";
        } else {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"] ?? "User";
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["role"] = $user["role"] ?? "user";

            if (isset($user["role"]) && $user["role"] === "admin") {
                header("Location: " . BASE_URL . "admin/dashboard.php");
                exit;
            }

            header("Location: " . BASE_URL . "user/dashboard.php");
            exit;
        }
    }
}

include "../includes/header.php";
?>

<section class="auth-section">
    <div class="auth-card">
        <p class="eyebrow">Welcome Back</p>
        <h1>Login</h1>

        <?php if ($error): ?>
            <div class="alert"><?= clean($error); ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <?= csrf_input(); ?>

            <label>Email</label>
            <input type="email" name="email" required placeholder="Enter your email">

            <label>Password</label>
            <input type="password" name="password" required placeholder="Enter your password">

            <button type="submit" class="btn btn-dark">Login</button>
        </form>

        <p class="auth-link">
            New customer? <a href="register.php">Create account</a>
        </p>
    </div>
</section>

<?php include "../includes/footer.php"; ?>
