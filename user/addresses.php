<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../includes/csrf.php";
require_once "../database/phase13-schema.php";

ensure_phase13_schema($conn);

$userId = (int)$_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $name = trim($_POST["full_name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address_line"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $state = trim($_POST["state"] ?? "");
    $pincode = trim($_POST["pincode"] ?? "");

    $stmt = $conn->prepare("INSERT INTO addresses (user_id, full_name, phone, address_line, city, state, pincode) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $userId, $name, $phone, $address, $city, $state, $pincode);
    $stmt->execute();

    $message = "Address saved successfully.";
}

$stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$addresses = $stmt->get_result();

$pageTitle = "Addresses | LOVA DUSK";
include "../includes/header.php";
?>

<section class="page-hero">
    <p class="eyebrow">Delivery</p>
    <h1>My Addresses</h1>
</section>

<section class="checkout-section">
    <?php if ($message): ?><div class="alert"><?= clean($message); ?></div><?php endif; ?>

    <div class="checkout-layout">
        <form method="POST" class="checkout-form">
            <?= csrf_input(); ?>
            <h2>Add Address</h2>

            <label>Full Name</label>
            <input type="text" name="full_name" required>

            <label>Phone</label>
            <input type="text" name="phone" required>

            <label>Address</label>
            <textarea name="address_line" required></textarea>

            <div class="form-row">
                <div><label>City</label><input type="text" name="city" required></div>
                <div><label>State</label><input type="text" name="state" required></div>
            </div>

            <label>Pincode</label>
            <input type="text" name="pincode" required>

            <button class="btn btn-dark">Save Address</button>
        </form>

        <aside class="cart-summary">
            <h2>Saved Addresses</h2>
            <?php if ($addresses->num_rows > 0): ?>
                <?php while ($address = $addresses->fetch_assoc()): ?>
                    <div class="summary-row address-row">
                        <span>
                            <?= clean($address["full_name"]); ?><br>
                            <?= clean($address["phone"]); ?><br>
                            <?= clean($address["address_line"]); ?>,
                            <?= clean($address["city"]); ?>,
                            <?= clean($address["state"]); ?> -
                            <?= clean($address["pincode"]); ?>
                        </span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No saved address yet.</p>
            <?php endif; ?>
        </aside>
    </div>
</section>

<?php include "../includes/footer.php"; ?>