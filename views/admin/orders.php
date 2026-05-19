<?php require 'views/layouts/header.php'; ?>
<h2>Manage Orders</h2>

<div class="card" style="margin-bottom: 1rem;">
    <form method="GET" action="<?php echo url('/admin/orders'); ?>" style="display: flex; gap: 1rem; align-items: center;">
        <?php // allow admin filtering so order list stays manageable ?>
        <label>Status:</label>
        <select name="status">
            <option value="">All</option>
            <option value="Pending" <?= $statusFilter === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Preparing" <?= $statusFilter === 'Preparing' ? 'selected' : '' ?>>Preparing</option>
            <option value="Out for Delivery" <?= $statusFilter === 'Out for Delivery' ? 'selected' : '' ?>>Out for Delivery</option>
            <option value="Delivered" <?= $statusFilter === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
        </select>
        <label>Date:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($dateFilter) ?>">
        <button type="submit" class="btn">Filter</button>
        <a href="<?php echo url('/admin/orders'); ?>" class="btn" style="background: #6c757d;">Clear</a>
    </form>
</div>

<?php if (empty($orders)): ?>
    <p>No orders found.</p>
<?php else: ?>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($order['user_name']) ?></strong><br>
                        <small style="color: #4b5563;"><?= htmlspecialchars($order['delivery_address']) ?></small>
                        <div style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed #e2e8f0; font-size: 0.85rem;">
                            <strong style="color: #475569;">Items Ordered:</strong>
                            <ul style="margin: 0.25rem 0 0 0; padding-left: 1.2rem; color: #475569;">
                                <?php foreach ($order['items'] as $item): ?>
                                    <li><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?> (৳<?= number_format($item['price'], 2) ?>)</li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </td>
                    <td>৳<?= number_format($order['total_amount'], 2) ?></td>
                    <td><?= date('M j, Y H:i', strtotime($order['created_at'])) ?></td>
                    <td>
                        <select class="status-select" data-original="<?= $order['status'] ?>" onchange="updateStatus(<?= $order['id'] ?>, this)" style="padding: 0.25rem; font-weight: bold; border-radius: 4px; border: 1px solid #ccc; color: #fff;">
                            <option value="Pending" <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Preparing" <?= $order['status'] === 'Preparing' ? 'selected' : '' ?>>Preparing</option>
                            <option value="Out for Delivery" <?= $order['status'] === 'Out for Delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                            <option value="Delivered" <?= $order['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                        </select>
                        <span id="status-indicator-<?= $order['id'] ?>" style="margin-left: 0.5rem; color: green; display: none;">&check;</span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<script>
const statusColors = {
    'Pending': '#ffc107',
    'Preparing': '#17a2b8',
    'Out for Delivery': '#007bff',
    'Delivered': '#28a745'
};

document.querySelectorAll('.status-select').forEach(select => {
    select.style.backgroundColor = statusColors[select.value];
    select.style.color = select.value === 'Pending' ? '#000' : '#fff';
});

async function updateStatus(id, selectElement) {
    const originalValue = selectElement.getAttribute('data-original');
    const newStatus = selectElement.value;
    
    try {
        const response = await fetch('<?php echo url("/api/orders/"); ?>' + id + '/status', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        });
        const result = await response.json();
        
        if (result.ok) {
            selectElement.setAttribute('data-original', newStatus);
            selectElement.style.backgroundColor = statusColors[newStatus];
            selectElement.style.color = newStatus === 'Pending' ? '#000' : '#fff';
            
            const ind = document.getElementById('status-indicator-' + id);
            ind.style.display = 'inline';
            setTimeout(() => ind.style.display = 'none', 2000);
        } else {
            alert(result.error || 'Failed to update status');
            selectElement.value = originalValue; // Revert
        }
    } catch(e) {
        alert('Network error');
        selectElement.value = originalValue; // Revert
    }
}
</script>
<?php require 'views/layouts/footer.php'; ?>
