<?php require 'views/layouts/header.php'; ?>

<div class="auth-container" style="max-width: 640px; margin: 3rem auto;">
    <div class="auth-card" style="padding: var(--spacing-xl);">
        <h2 class="auth-title" style="text-align: left; border-bottom: 3px solid var(--brand-red); padding-bottom: var(--spacing-xs); margin-bottom: var(--spacing-lg);">Checkout</h2>

        <div style="background: #f1f5f9; padding: var(--spacing-md); border-radius: var(--radius-md); margin-bottom: var(--spacing-lg); border: 1px solid var(--color-border); display: flex; flex-direction: column; gap: var(--spacing-xs);">
            <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--brand-blue); margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.05em;">Order Summary</h3>
            <div style="display: flex; justify-content: space-between; font-size: 0.95rem; color: var(--color-text-primary);">
                <span>Total Items:</span>
                <span style="font-weight: 700;"><?= isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0 ?> items</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 1.1rem; color: var(--brand-blue); padding-top: var(--spacing-xs); border-top: 1.5px dashed var(--color-border);">
                <strong>Total Amount:</strong>
                <strong style="font-weight: 900;">৳<?= number_format($total ?? 0, 2) ?></strong>
            </div>
        </div>
        
        <form action="<?php echo url('/checkout'); ?>" method="POST">
            <div class="form-group">
                <label class="form-label-custom">Delivery Address</label>
                <textarea name="address" class="form-input-custom" required rows="4" placeholder="Enter shipping address for this order..." style="resize: vertical; min-height: 90px; font-family: inherit; line-height: 1.5;"><?= htmlspecialchars($_SESSION['old']['address'] ?? $user['delivery_address'] ?? '') ?></textarea>
                <span style="display: block; font-size: 0.75rem; color: var(--color-text-muted); margin-top: 4px;">You can modify this address specifically for this delivery.</span>
            </div>
            
            <div class="form-group" style="margin-top: var(--spacing-lg); margin-bottom: var(--spacing-xl);">
                <label class="form-label-custom">Payment Method</label>
                <div style="display: flex; gap: var(--spacing-lg); margin-top: var(--spacing-xs); flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.95rem; font-weight: 600; color: var(--color-text-primary); cursor: pointer; user-select: none;">
                        <input type="radio" name="payment_method" value="Cash" <?= (($_SESSION['old']['payment_method'] ?? 'Cash') === 'Cash') ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--brand-blue); cursor: pointer; margin: 0;"> 
                        Cash on Delivery
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.95rem; font-weight: 600; color: var(--color-text-primary); cursor: pointer; user-select: none;">
                        <input type="radio" name="payment_method" value="Card" <?= (($_SESSION['old']['payment_method'] ?? '') === 'Card') ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--brand-blue); cursor: pointer; margin: 0;"> 
                        Credit / Debit Card
                    </label>
                </div>
            </div>
            
            <button type="submit" class="btn-auth-submit" style="background: var(--brand-orange); box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25); font-size: 1.1rem; padding: 0.9rem var(--spacing-md);">Place Order</button>
        </form>
    </div>
</div>

<?php 
unset($_SESSION['old']); 
require 'views/layouts/footer.php'; 
?>
