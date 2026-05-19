<?php require 'views/layouts/header.php'; ?>

<div style="margin-bottom: var(--spacing-lg);">
    <h2 style="font-size: 1.8rem; font-weight: 800; color: var(--brand-blue); margin: 0; display: flex; align-items: center; gap: 8px;">
        🛒 Your Cart
    </h2>
    <p style="color: var(--color-text-muted); margin: 4px 0 0 0; font-size: 0.95rem;">
        Review your freshly selected items and adjust portions before checking out.
    </p>
</div>

<?php if (empty($_SESSION['cart'])): ?>
    
    <div style="background: #ffffff; border-radius: var(--radius-md); box-shadow: var(--shadow-tactile); padding: var(--spacing-xl) var(--spacing-lg); text-align: center; border: 1px solid var(--color-border);">
        <div style="width: 80px; height: 80px; background: #fee2e2; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--spacing-lg) auto; color: var(--brand-red);">
            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
        </div>
        <h3 style="font-size: 1.3rem; font-weight: 700; color: var(--brand-blue); margin: 0 0 var(--spacing-xs) 0;">Your Cart is Empty</h3>
        <p style="color: var(--color-text-muted); font-size: 0.95rem; max-width: 320px; margin: 0 auto var(--spacing-lg) auto; line-height: 1.5;">
            Looks like you haven't added anything to your cart yet. Explore our delicious menu to find your favorites!
        </p>
        <a href="<?php echo url('/menu'); ?>" class="btn-checkout" style="display: inline-block; width: auto; padding: 0.8rem 2rem; margin-top: 0;">
            🍕 Browse Menu
        </a>
    </div>
<?php else: ?>
    
    <div class="cart-layout">

        <div class="cart-items-list" id="cart-container">
            <?php 
            $grandTotal = 0;
            $menuModel = new MenuItem($this->pdo); 
            
            foreach ($_SESSION['cart'] as $id => $item): 
                $subtotal = $item['price'] * $item['quantity'];
                $grandTotal += $subtotal;

                $dbItem = $menuModel->findById($id);
                $imagePath = $dbItem ? $dbItem['image_path'] : '';
            ?>
            <div class="cart-item-card" id="cart-row-<?= $id ?>" data-price="<?= $item['price'] ?>">

                <?php if ($imagePath): ?>
                    <img src="<?php echo url('/' . htmlspecialchars($imagePath)); ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-item-img">
                <?php else: ?>
                    <div class="cart-item-img" style="background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: var(--color-text-muted);">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </div>
                <?php endif; ?>

                <div class="cart-item-info">
                    <h3 class="cart-item-title"><?= htmlspecialchars($item['name']) ?></h3>
                    <span class="cart-item-unit-price">৳<?= number_format($item['price'], 2) ?> each</span>
                    
                    <div class="cart-item-meta">
                        
                        <div class="quantity-controller">
                            <button class="quantity-btn" onclick="updateQuantity(<?= $id ?>, 'decrease')">−</button>
                            <span class="quantity-value" id="qty-<?= $id ?>"><?= $item['quantity'] ?></span>
                            <button class="quantity-btn" onclick="updateQuantity(<?= $id ?>, 'increase')">+</button>
                        </div>

                        <button class="btn-remove-item" onclick="removeFromCart(<?= $id ?>)">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            <span>Remove</span>
                        </button>
                    </div>
                </div>

                <div class="cart-item-price-block">
                    <span class="cart-item-price" id="subtotal-<?= $id ?>">৳<?= number_format($subtotal, 2) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="summary-card">
            <h3 class="summary-title">Order Summary</h3>
            
            <div class="summary-row">
                <span>Subtotal</span>
                <span>৳<?= number_format($grandTotal, 2) ?></span>
            </div>
            
            <div class="summary-row">
                <span>Delivery Charge</span>
                <span style="color: #10b981; font-weight: 600;">FREE</span>
            </div>
            
            <div class="summary-row">
                <span>Govt VAT (5%)</span>
                <span>৳0.00</span>
            </div>
            
            <div class="summary-row total-row">
                <span>Grand Total</span>
                <span id="grand-total">৳<?= number_format($grandTotal, 2) ?></span>
            </div>

            <a href="<?php echo url('/checkout'); ?>" class="btn-checkout">
                Proceed to Checkout
            </a>
            
            <a href="<?php echo url('/menu'); ?>" class="btn-continue-shopping">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span>Add More Items</span>
            </a>
        </div>
        
    </div>
<?php endif; ?>

<script>

async function updateQuantity(id, action) {
    const result = await fetchJson('<?php echo url("/api/cart/update"); ?>', { id: id, action: action });
    if (result.success) {
        
        document.getElementById('cart-count').textContent = result.cart_count;
        
        const qtySpan = document.getElementById('qty-' + id);
        let currentQty = parseInt(qtySpan.textContent);
        if (action === 'increase') {
            currentQty++;
        } else {
            currentQty--;
        }
        
        if (currentQty <= 0) {
            
            const row = document.getElementById('cart-row-' + id);
            row.style.opacity = '0';
            row.style.transform = 'scale(0.9)';
            setTimeout(() => {
                row.remove();
                if (result.cart_count === 0) {
                    location.reload(); 
                }
            }, 250);
        } else {
            qtySpan.textContent = currentQty;

            const card = document.getElementById('cart-row-' + id);
            const price = parseFloat(card.dataset.price);
            document.getElementById('subtotal-' + id).textContent = '৳' + (price * currentQty).toFixed(2);
        }

        document.getElementById('grand-total').textContent = '৳' + result.total;

        const subtotalRow = document.querySelector('.summary-row span:last-child');
        if (subtotalRow) {
            subtotalRow.textContent = '৳' + result.total;
        }
    }
}

async function removeFromCart(id) {
    const result = await fetchJson('<?php echo url("/api/cart/remove"); ?>', { id: id });
    if (result.success) {
        
        document.getElementById('cart-count').textContent = result.cart_count;

        const row = document.getElementById('cart-row-' + id);
        row.style.opacity = '0';
        row.style.transform = 'scale(0.9)';
        setTimeout(() => {
            row.remove();
            
            if (result.cart_count === 0) {
                location.reload(); 
            }
        }, 250);

        document.getElementById('grand-total').textContent = '৳' + result.total;
        const subtotalRow = document.querySelector('.summary-row span:last-child');
        if (subtotalRow) {
            subtotalRow.textContent = '৳' + result.total;
        }
    }
}
</script>

<?php require 'views/layouts/footer.php'; ?>
