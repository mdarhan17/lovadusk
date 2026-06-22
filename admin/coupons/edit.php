<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/csrf.php";
require_once "../../database/phase8-schema.php";

ensure_phase8_schema($conn);

$id = (int)($_GET["id"] ?? 0);
$message = "";

$stmt = $conn->prepare("SELECT * FROM coupons WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$coupon = $stmt->get_result()->fetch_assoc();

if (!$coupon) {
    die("Coupon not found.");
}

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

    $stmt = $conn->prepare("UPDATE coupons SET code = ?, discount_type = ?, discount_value = ?, min_order = ?, max_discount = ?, starts_at = ?, expires_at = ?, usage_limit = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssdddssisi", $code, $type, $value, $minOrder, $maxDiscount, $startsAt, $expiresAt, $usageLimit, $status, $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    }

    $message = "Coupon update failed.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Coupon | Admin</title>
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
            <p class="eyebrow">Update Discount</p>
            <h1>Edit Coupon</h1>
        </div>
        <a href="index.php" class="btn btn-light">Back</a>
    </div>

    <section class="admin-panel">
        <?php if ($message): ?><div class="alert"><?= clean($message); ?></div><?php endif; ?>

        <form method="POST" class="admin-form">
            <?= csrf_input(); ?>

            <label>Coupon Code</label>
            <input type="text" name="code" value="<?= clean($coupon["code"]); ?>" required>

            <label>Discount Type</label>
            <select name="discount_type">
                <option value="fixed" <?= $coupon["discount_type"] === "fixed" ? "selected" : ""; ?>>Fixed Amount</option>
                <option value="percentage" <?= $coupon["discount_type"] === "percentage" ? "selected" : ""; ?>>Percentage</option>
            </select>

            <label>Discount Value</label>
            <input type="number" step="0.01" name="discount_value" value="<?= clean($coupon["discount_value"]); ?>" required>

            <div class="form-row">
                <div>
                    <label>Minimum Order</label>
                    <input type="number" step="0.01" name="min_order" value="<?= clean($coupon["min_order"]); ?>">
                </div>
                <div>
                    <label>Max Discount</label>
                    <input type="number" step="0.01" name="max_discount" value="<?= clean($coupon["max_discount"]); ?>">
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label>Starts At</label>
                    <input type="datetime-local" name="starts_at" value="<?= $coupon["starts_at"] ? date("Y-m-d\TH:i", strtotime($coupon["starts_at"])) : ""; ?>">
                </div>
                <div>
                    <label>Expires At</label>
                    <input type="datetime-local" name="expires_at" value="<?= $coupon["expires_at"] ? date("Y-m-d\TH:i", strtotime($coupon["expires_at"])) : ""; ?>">
                </div>
            </div>

            <label>Usage Limit</label>
            <input type="number" name="usage_limit" value="<?= (int)$coupon["usage_limit"]; ?>">

            <label>Status</label>
            <select name="status">
                <option value="active" <?= $coupon["status"] === "active" ? "selected" : ""; ?>>Active</option>
                <option value="inactive" <?= $coupon["status"] === "inactive" ? "selected" : ""; ?>>Inactive</option>
            </select>

            <button type="submit" class="btn btn-dark">Update Coupon</button>
        </form>
    </section>
</main>
</body>
</html>