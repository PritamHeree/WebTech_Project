<?php require 'views/layouts/header.php'; ?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2>Checkout</h2>
    <div style="background: #f8f9fa; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
        <h3>Order Summary</h3>
        <p><strong>Total Amount:</strong> ৳<?= number_format($total ?? 0, 2) ?></p>
        <p><strong>Items:</strong> <?= isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0 ?></p>
    </div>
    
    <form action="<?php echo url('/checkout'); ?>" method="POST">
        <div class="form-group">
            <label>Delivery Address</label>
            <textarea name="address" required rows="4"><?= htmlspecialchars($_SESSION['old']['address'] ?? $user['delivery_address'] ?? '') ?></textarea>
            <small style="color: #666;">You can edit this address for this specific order.</small>
        </div>
        
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label><strong>Payment Method</strong></label>
            <div style="display: flex; gap: 2rem; margin-top: 0.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="radio" name="payment_method" value="Cash" <?= (($_SESSION['old']['payment_method'] ?? 'Cash') === 'Cash') ? 'checked' : '' ?> style="width: auto; margin: 0;"> Cash on Delivery
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="radio" name="payment_method" value="Card" <?= (($_SESSION['old']['payment_method'] ?? '') === 'Card') ? 'checked' : '' ?> style="width: auto; margin: 0;"> Credit/Debit Card
                </label>
            </div>
        </div>
        
        <button type="submit" class="btn btn-success" style="width: 100%; font-size: 1.1rem;">Place Order</button>
    </form>
    <?php unset($_SESSION['old']); // clear old input after rendering so it doesn't persist unexpectedly ?>
</div>
<?php require 'views/layouts/footer.php'; ?>
