<?php require 'views/layouts/header.php'; ?>
<h2>My Orders</h2>
<?php if (empty($orders)): ?>
    <p>You have no orders yet.</p>
<?php else: ?>
    <?php // show a summary of each order and let users expand details ?>
    <?php foreach ($orders as $order): ?>
        <div class="card" id="order-<?= $order['id'] ?>">
            <div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="toggleDetails(<?= $order['id'] ?>)">
                <div>
                    <strong>Order #<?= $order['id'] ?></strong><br>
                    <small><?= date('M j, Y H:i', strtotime($order['created_at'])) ?></small>
                </div>
                <div>
                    <strong>৳<?= number_format($order['total_amount'], 2) ?></strong>
                </div>
                <div>
                    <?php
                        $badgeClass = 'badge-pending';
                        if ($order['status'] == 'Preparing') $badgeClass = 'badge-preparing';
                        if ($order['status'] == 'Out for Delivery') $badgeClass = 'badge-delivery';
                        if ($order['status'] == 'Delivered') $badgeClass = 'badge-delivered';
                    ?>
                    <span class="badge <?= $badgeClass ?>" id="status-badge-<?= $order['id'] ?>"><?= $order['status'] ?></span>
                    <span style="font-size: 1.2rem; margin-left: 1rem;">&#9660;</span>
                </div>
            </div>
            
            <div id="details-<?= $order['id'] ?>" style="display: none; margin-top: 1rem; border-top: 1px solid #eee; padding-top: 1rem;">
                <h4>Items:</h4>
                <ul>
                    <?php foreach ($order['items'] as $item): ?>
                        <li><?= $item['quantity'] ?>x <?= htmlspecialchars($item['name']) ?> - ৳<?= number_format($item['price'], 2) ?></li>
                    <?php endforeach; ?>
                </ul>
                <p><strong>Delivery Address:</strong><br><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
function toggleDetails(id) {
    const details = document.getElementById('details-' + id);
    if (details.style.display === 'none') {
        details.style.display = 'block';
    } else {
        details.style.display = 'none';
    }
}

// Status Polling for active orders
// refresh
const activeOrders = <?= json_encode(array_values(array_filter($orders, function($o) { return $o['status'] !== 'Delivered'; }))) ?>;

activeOrders.forEach(order => {
    const intervalId = setInterval(async () => {
        try {
            const response = await fetch('<?php echo url("/api/orders/"); ?>' + order.id + '/status');
            const data = await response.json();
            
            if (data.success) {
                const badge = document.getElementById('status-badge-' + order.id);
                if (badge && badge.textContent !== data.status) {
                    badge.textContent = data.status;
                    badge.className = 'badge';
                    if (data.status === 'Pending') badge.classList.add('badge-pending');
                    if (data.status === 'Preparing') badge.classList.add('badge-preparing');
                    if (data.status === 'Out for Delivery') badge.classList.add('badge-delivery');
                    if (data.status === 'Delivered') {
                        badge.classList.add('badge-delivered');
                        clearInterval(intervalId); // Stop polling this order
                    }
                }
            }
        } catch (e) {
            console.error("Polling error for order " + order.id, e);
        }
    }, 10000);
});
</script>
<?php require 'views/layouts/footer.php'; ?>
