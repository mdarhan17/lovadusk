<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/csrf.php";
require_once "../../database/phase8-schema.php";

ensure_phase8_schema($conn);

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $code = strtoupper(trim($_POST["code"] ?? ""));
    $type = $_POST["discount_type"] ?? "fixed";
    $value = (float)($_POST["discount_value"] ?? 0);
    $minOrder = (float)($_POST["min_order"] ?? 0);
    $maxDiscount = (float)($_POST["max_discount"] ?? 0);
    $startsAt = trim($_POST["starts_at"] ?? "");
    $expiresAt = trim($_POST["expires_at"] ?? "");
    $usageLimit = (int)($_POST["usage_limit"] ?? 0);
    $status = $_POST["status"] ?? "active";

    if ($code === "" || $value <= 0) {
        $message = "Coupon code and discount value are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO coupons (code, discount_type, discount_value, min_order, max_discount, starts_at, expires_at, usage_limit, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdddssis", $code, $type, $value, $minOrder, $maxDiscount, $startsAt, $expiresAt, $usageLimit, $status);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        }

        $message = "Coupon could not be added. Code may already exist.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Coupon | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="index.php">Coupons</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">New Discount</p>
            <h1>Add Coupon</h1>
        </div>
        <a href="index.php" class="btn btn-light">Back</a>
    </div>

    <section class="admin-panel">
        <?php if ($message): ?><div class="alert"><?= clean($message); ?></div><?php endif; ?>

        <form method="POST" class="admin-form">
            <?= csrf_input(); ?>

            <label>Coupon Code</label>
            <input type="text" name="code" placeholder="LOVA10" required>

            <label>Discount Type</label>
            <select name="discount_type">
                <option value="fixed">Fixed Amount</option>
                <option value="percentage">Percentage</option>
            </select>

            <label>Discount Value</label>
            <input type="number" step="0.01" name="discount_value" required>

            <div class="form-row">
                <div>
                    <label>Minimum Order</label>
                    <input type="number" step="0.01" name="min_order" value="0">
                </div>
                <div>
                    <label>Max Discount</label>
                    <input type="number" step="0.01" name="max_discount" value="0">
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label>Starts At</label>
                    <input type="datetime-local" name="starts_at">
                </div>
                <div>
                    <label>Expires At</label>
                    <input type="datetime-local" name="expires_at">
                </div>
            </div>

            <label>Usage Limit</label>
            <input type="number" name="usage_limit" value="0">

            <label>Status</label>
            <select name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <button type="submit" class="btn btn-dark">Save Coupon</button>
        </form>
    </section>
</main>
</body>
</html>