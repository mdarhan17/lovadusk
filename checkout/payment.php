<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../config/razorpay.php";
require_once "../includes/csrf.php";
require_once "../database/phase5-schema.php";

ensure_phase5_schema($conn);

$userId = (int)$_SESSION["user_id"];
$orderNumber = trim($_GET["order"] ?? "");

$stmt = $conn->prepare("SELECT * FROM orders WHERE order_number = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("si", $orderNumber, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found.");
}

if ($order["payment_status"] === "paid") {
    header("Location: success.php?order=" . urlencode($orderNumber));
    exit;
}

$pageTitle = "Payment | LOVA DUSK";
include "../includes/header.php";
?>

<section class="success-section">
    <p class="eyebrow">Razorpay Payment</p>
    <h1>Complete Payment</h1>
    <p>Order Number: <?= clean($order["order_number"]); ?></p>

    <div class="success-box">
        <p><strong>Customer:</strong> <?= clean($order["customer_name"]); ?></p>
        <p><strong>Total:</strong> <?= money($order["total_amount"]); ?></p>
        <p><strong>Payment:</strong> Razorpay</p>
    </div>

    <button id="payNowBtn" class="btn btn-dark">Pay Now</button>
    <p id="paymentMessage" class="checkout-note"></p>
</section>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
const payButton = document.getElementById("payNowBtn");
const messageBox = document.getElementById("paymentMessage");

payButton.addEventListener("click", async function () {
    payButton.disabled = true;
    messageBox.textContent = "Creating secure payment order...";

    try {
        const formData = new FormData();
        formData.append("order_number", "<?= clean($order["order_number"]); ?>");
        formData.append("csrf_token", "<?= csrf_token(); ?>");

        const createResponse = await fetch("<?= BASE_URL; ?>payment/create-order.php", {
            method: "POST",
            body: formData
        });

        const createData = await createResponse.json();

        if (!createData.success) {
            messageBox.textContent = createData.message || "Unable to create payment order.";
            payButton.disabled = false;
            return;
        }

        const options = {
            key: "<?= RAZORPAY_KEY_ID; ?>",
            amount: createData.amount,
            currency: "INR",
            name: "LOVA DUSK",
            description: "Luxury Fashion Order",
            order_id: createData.razorpay_order_id,
            prefill: {
                name: "<?= clean($order["customer_name"]); ?>",
                email: "<?= clean($order["customer_email"]); ?>",
                contact: "<?= clean($order["customer_phone"]); ?>"
            },
            theme: {
                color: "#111111"
            },
            handler: async function (response) {
                messageBox.textContent = "Verifying payment...";

                const verifyData = new FormData();
                verifyData.append("order_number", "<?= clean($order["order_number"]); ?>");
                verifyData.append("razorpay_order_id", response.razorpay_order_id);
                verifyData.append("razorpay_payment_id", response.razorpay_payment_id);
                verifyData.append("razorpay_signature", response.razorpay_signature);
                verifyData.append("csrf_token", "<?= csrf_token(); ?>");

                const verifyResponse = await fetch("<?= BASE_URL; ?>payment/verify-payment.php", {
                    method: "POST",
                    body: verifyData
                });

                const verifyResult = await verifyResponse.json();

                if (verifyResult.success) {
                    window.location.href = verifyResult.redirect;
                } else {
                    messageBox.textContent = verifyResult.message || "Payment verification failed.";
                    payButton.disabled = false;
                }
            },
            modal: {
                ondismiss: function () {
                    messageBox.textContent = "Payment cancelled.";
                    payButton.disabled = false;
                }
            }
        };

        const razorpay = new Razorpay(options);
        razorpay.open();
    } catch (error) {
        messageBox.textContent = "Payment error. Please try again.";
        payButton.disabled = false;
    }
});
</script>

<?php include "../includes/footer.php"; ?>