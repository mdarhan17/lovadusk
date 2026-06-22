<?php
function render_invoice_html($order, $items) {
    ob_start();
    ?>
    <div class="invoice-box">
        <div class="invoice-head">
            <div>
                <h1>LOVA DUSK</h1>
                <p>Luxury Fashion Drops</p>
            </div>
            <div class="invoice-meta">
                <h2>INVOICE</h2>
                <p>Order: <?= htmlspecialchars($order["order_number"]); ?></p>
                <p>Date: <?= htmlspecialchars($order["created_at"]); ?></p>
            </div>
        </div>

        <div class="invoice-grid">
            <div>
                <h3>Billed To</h3>
                <p><?= htmlspecialchars($order["customer_name"]); ?></p>
                <p><?= htmlspecialchars($order["customer_email"]); ?></p>
                <p><?= htmlspecialchars($order["customer_phone"]); ?></p>
            </div>

            <div>
                <h3>Shipping Address</h3>
                <p><?= htmlspecialchars($order["address_line"]); ?></p>
                <p><?= htmlspecialchars($order["city"]); ?>, <?= htmlspecialchars($order["state"]); ?> - <?= htmlspecialchars($order["pincode"]); ?></p>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item["name"]); ?></td>
                        <td><?= htmlspecialchars($item["size"]); ?></td>
                        <td><?= (int)$item["qty"]; ?></td>
                        <td><?= money($item["price"]); ?></td>
                        <td><?= money((float)$item["price"] * (int)$item["qty"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="invoice-total">
            <p><span>Payment Status:</span> <?= htmlspecialchars($order["payment_status"]); ?></p>
            <p><span>Order Status:</span> <?= htmlspecialchars($order["order_status"]); ?></p>
            <h2>Total: <?= money($order["total_amount"]); ?></h2>
        </div>

        <div class="invoice-footer">
            <p>Thank you for shopping with LOVA DUSK.</p>
            <p>This is a system generated invoice.</p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>