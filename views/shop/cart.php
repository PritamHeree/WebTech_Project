<?php require 'views/layouts/header.php'; ?>
<h2>Your Cart</h2>

<?php if (empty($_SESSION['cart'])): ?>
    <div class="card">
        <p>Your cart is empty. <a href="<?php echo url('/menu'); ?>">Browse Menu</a></p>
    </div>
<?php else: ?>
    <div class="card" id="cart-container">
        <table id="cart-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grandTotal = 0;
                foreach ($_SESSION['cart'] as $id => $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $grandTotal += $subtotal;
                ?>
                <tr id="cart-row-<?= $id ?>">
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td>৳<?= number_format($item['price'], 2) ?></td>
                    <td>
                        <button class="btn" style="padding: 0.1rem 0.5rem;" onclick="updateQuantity(<?= $id ?>, 'decrease')">-</button>
                        <span id="qty-<?= $id ?>" style="margin: 0 0.5rem;"><?= $item['quantity'] ?></span>
                        <button class="btn" style="padding: 0.1rem 0.5rem;" onclick="updateQuantity(<?= $id ?>, 'increase')">+</button>
                    </td>
                    <td id="subtotal-<?= $id ?>">৳<?= number_format($subtotal, 2) ?></td>
                    <td>
                        <button class="btn btn-danger" style="padding: 0.2rem 0.5rem;" onclick="removeFromCart(<?= $id ?>)">Remove</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" style="text-align: right;">Grand Total:</th>
                    <th id="grand-total">৳<?= number_format($grandTotal, 2) ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        
        <div style="text-align: right; margin-top: 1rem;">
            <a href="<?php echo url('/checkout'); ?>" class="btn btn-success" style="font-size: 1.2rem;">Proceed to Checkout</a>
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
        if (action === 'increase') currentQty++;
        else currentQty--;
        
        // keep the client-side totals in sync with server response
        if (currentQty <= 0) {
            document.getElementById('cart-row-' + id).remove();
        } else {
            qtySpan.textContent = currentQty;
            const price = parseFloat(document.querySelector(`#cart-row-${id} td:nth-child(2)`).textContent.replace('৳', ''));
            document.getElementById('subtotal-' + id).textContent = '৳' + (price * currentQty).toFixed(2);
        }
        
        document.getElementById('grand-total').textContent = '৳' + result.total;
        
        if (result.cart_count === 0) {
            location.reload(); // Reload to show empty cart message
        }
    }
}

async function removeFromCart(id) {
    const result = await fetchJson('<?php echo url("/api/cart/remove"); ?>', { id: id });
    if (result.success) {
        document.getElementById('cart-count').textContent = result.cart_count;
        document.getElementById('cart-row-' + id).remove();
        document.getElementById('grand-total').textContent = '৳' + result.total;
        
        // reload page when cart becomes empty so the empty state render is consistent
        if (result.cart_count === 0) {
            location.reload();
        }
    }
}
</script>
<?php require 'views/layouts/footer.php'; ?>
