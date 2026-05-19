<?php require 'views/layouts/header.php'; ?>
<div class="card" style="max-width: 600px; margin: 0 auto; text-align: center; padding: 2rem;">
    <div style="color: #28a745; font-size: 3rem; margin-bottom: 1rem;">&check;</div>
    <h2>Order Confirmed!</h2>
    <p>Thank you for your order. Your order has been received and is being processed.</p>
    
    <div style="background: #f8f9fa; padding: 1.5rem; text-align: left; margin: 2rem 0; border-radius: 4px; border: 1px solid #dee2e6;">
        <h3 style="margin-top: 0;">Order Information</h3>
        <?php ?>
        <p><strong>Order ID:</strong> #<?= htmlspecialchars($order['id']) ?></p>
        <p><strong>Date:</strong> <?= date('M j, Y H:i', strtotime($order['created_at'])) ?></p>
        <p><strong>Status:</strong> <span class="badge badge-pending"><?= htmlspecialchars($order['status']) ?></span></p>
        <p><strong>Delivery Address:</strong> <?= nl2br(htmlspecialchars($order['delivery_address'])) ?></p>
        
        <hr style="margin: 1rem 0; border: 0; border-top: 1px solid #dee2e6;">
        
        <h4 style="margin-bottom: 0.5rem;">Items Summary:</h4>
        <ul style="padding-left: 1.5rem; margin: 0;">
            <?php foreach ($items as $item): ?>
                <li style="margin-bottom: 0.25rem;">
                    <?= $item['quantity'] ?>x <?= htmlspecialchars($item['name']) ?> - ৳<?= number_format($item['price'] * $item['quantity'], 2) ?>
                </li>
            <?php endforeach; ?>
        </ul>
        
        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #dee2e6; display: flex; justify-content: space-between; font-weight: bold;">
            <span>Total Paid:</span>
            <span>৳<?= number_format($order['total_amount'], 2) ?></span>
        </div>
    </div>
    
    <div>
        <a href="<?php echo url('/my-orders'); ?>" class="btn">Track Order</a>
        <a href="<?php echo url('/menu'); ?>" class="btn" style="background: #6c757d; margin-left: 0.5rem;">Back to Menu</a>
    </div>
</div>
<?php require 'views/layouts/footer.php'; ?>
