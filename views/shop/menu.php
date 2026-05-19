<?php require 'views/layouts/header.php'; ?>

<!-- Sticky PWA Category Navigation Bar (Domino's Discovery inspired) -->
<?php 
// Group items by category to construct both our sticky categories index and the structured grid panels
$grouped = [];
foreach ($items as $item) {
    $grouped[$item['category_name']][] = $item;
}
?>

<div class="category-nav-wrapper">
    <div class="category-scroller">
        <a href="#all" class="category-pill active" onclick="scrollToCategory(event, 'all')">
            🍕 All Items
        </a>
        <?php foreach (array_keys($grouped) as $catName): ?>
            <?php 
                // Display custom emojis per category for an expressive, visual touch
                $emoji = '🍽️';
                if (stripos($catName, 'pizza') !== false) $emoji = '🍕';
                elseif (stripos($catName, 'drink') !== false || stripos($catName, 'beverage') !== false) $emoji = '🥤';
                elseif (stripos($catName, 'dessert') !== false || stripos($catName, 'sweet') !== false) $emoji = '🍰';
                elseif (stripos($catName, 'side') !== false || stripos($catName, 'appetizer') !== false) $emoji = '🍟';
            ?>
            <a href="#cat-<?= md5($catName) ?>" class="category-pill" onclick="scrollToCategory(event, 'cat-<?= md5($catName) ?>')">
                <?= $emoji ?> <?= htmlspecialchars($catName) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Header Branding & Search Wrapper -->
<div style="display: flex; flex-direction: column; gap: var(--spacing-sm); margin-bottom: var(--spacing-lg);">
    <h2 style="font-size: 1.8rem; font-weight: 800; color: var(--brand-blue); margin: 0; display: flex; align-items: center; gap: 8px;">
        Explore Our Menu
    </h2>
    <p style="color: var(--color-text-muted); margin: 0; font-size: 0.95rem;">
        Freshly baked flavors right to your doorstep. Tap any item to build your perfect meal.
    </p>
    
    <div class="search-wrapper" style="margin-top: var(--spacing-sm);">
        <input type="text" id="search" class="search-input" placeholder="Search for pizzas, sides, drinks..." onkeyup="filterMenu()">
        <!-- Magnifying glass SVG icon embedded inside the search bar -->
        <span style="position: absolute; right: var(--spacing-md); top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </span>
    </div>
</div>

<!-- Main Menu Catalog Container -->
<div id="menu-container">
    <?php foreach ($grouped as $categoryName => $categoryItems): ?>
        <div id="cat-<?= md5($categoryName) ?>" style="scroll-margin-top: 100px;">
            <h3 class="category-header">
                <span><?= htmlspecialchars($categoryName) ?></span>
                <span style="font-size: 0.9rem; font-weight: 500; color: var(--color-text-muted); background: #f1f5f9; padding: 0.2rem 0.6rem; border-radius: var(--radius-full);">
                    <?= count($categoryItems) ?> items
                </span>
            </h3>
            
            <div class="menu-grid">
                <?php foreach ($categoryItems as $item): ?>
                    <div class="menu-item-card" data-name="<?= strtolower(htmlspecialchars($item['name'])) ?>" data-cat="<?= strtolower(htmlspecialchars($item['category_name'])) ?>">
                        
                        <!-- Image Container with visual zoom effect -->
                        <div class="card-img-container">
                            <?php if ($item['image_path']): ?>
                                <img src="<?php echo url('/' . htmlspecialchars($item['image_path'])); ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
                            <?php else: ?>
                                <!-- Fallback SVG when thumbnail is missing -->
                                <div style="width: 100%; height: 100%; background: #e2e8f0; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--color-text-muted); gap: var(--spacing-xs);">
                                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                    <span style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Fresh Choice</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php 
                                $isVeg = (stripos($item['name'], 'veg') !== false || stripos($item['description'], 'veg') !== false);
                                if ($isVeg): 
                            ?>
                                <span class="card-badge badge-tag-green">Veg</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Card Body Context -->
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="card-desc"><?= htmlspecialchars($item['description']) ?></p>
                            
                            <!-- Pricing & Primary Tap Action Targets -->
                            <div class="card-actions">
                                <div class="card-price">
                                    ৳<?= number_format($item['price'], 2) ?>
                                </div>
                                <button class="btn-add-to-cart" onclick="addToCart(<?= $item['id'] ?>, event)">
                                    <span>Add</span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- PWA Mobile Floating Cart Bar -->
<?php 
$cartCount = 0;
$cartTotal = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
    foreach ($_SESSION['cart'] as $cartItem) {
        $cartTotal += $cartItem['price'] * $cartItem['quantity'];
    }
}
if ($cartCount > 0):
?>
    <a href="<?php echo url('/cart'); ?>" class="mobile-floating-cart">
        <span style="font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
            <span style="background: rgba(255,255,255,0.2); padding: 0.2rem 0.6rem; border-radius: var(--radius-sm);"><?= $cartCount ?></span>
            <span>৳<?= number_format($cartTotal, 2) ?> Total</span>
        </span>
        <span style="font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 4px;">
            View Cart
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </span>
    </a>
<?php endif; ?>

<script>
/**
 * Smooth Category scrolling replicating native app touch selectors.
 */
function scrollToCategory(event, id) {
    event.preventDefault();
    
    // Highlight selected pill
    document.querySelectorAll('.category-pill').forEach(pill => {
        pill.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    
    if (id === 'all') {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        return;
    }
    
    const targetElement = document.getElementById(id);
    if (targetElement) {
        // Offset for the sticky category header scroller
        const offset = 80; 
        const elementPosition = targetElement.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - offset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
}

/**
 * Filter menu items dynamically in AJAX and re-render using the same premium component-based HTML.
 */
async function filterMenu() {
    const query = document.getElementById('search').value;
    try {
        const response = await fetch('<?php echo url("/api/menu-items/search"); ?>?q=' + encodeURIComponent(query));
        const result = await response.json();
        
        if (result.ok) {
            const container = document.getElementById('menu-container');
            container.innerHTML = '';
            
            // Group the AJAX response categories
            const grouped = {};
            result.items.forEach(item => {
                if (!grouped[item.category_name]) {
                    grouped[item.category_name] = [];
                }
                grouped[item.category_name].push(item);
            });
            
            if (Object.keys(grouped).length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: var(--spacing-xl) 0; color: var(--color-text-muted);">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: var(--spacing-sm);"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <p style="font-size: 1.1rem; font-weight: 600; margin: 0;">No matching items found</p>
                        <p style="font-size: 0.9rem; margin-top: 4px;">Try searching for a different keyword!</p>
                    </div>`;
                return;
            }
            
            // Generate exact premium cards dynamically for search results
            for (const catName in grouped) {
                let catHtml = `
                <div id="cat-${catName.replace(/\s+/g, '-').toLowerCase()}" style="scroll-margin-top: 100px;">
                    <h3 class="category-header">
                        <span>${catName}</span>
                        <span style="font-size: 0.9rem; font-weight: 500; color: var(--color-text-muted); background: #f1f5f9; padding: 0.2rem 0.6rem; border-radius: var(--radius-full);">
                            ${grouped[catName].length} items
                        </span>
                    </h3>
                    <div class="menu-grid">`;
                
                grouped[catName].forEach(item => {
                    const imgHtml = item.image_path 
                        ? `<img src="<?php echo url('/'); ?>/${item.image_path}" alt="${item.name}" loading="lazy">`
                        : `<div style="width: 100%; height: 100%; background: #e2e8f0; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--color-text-muted); gap: var(--spacing-xs);">
                                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                <span style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Fresh Choice</span>
                           </div>`;
                    
                    const isVeg = (item.name.toLowerCase().includes('veg') || item.description.toLowerCase().includes('veg'));
                    const badgeHtml = isVeg 
                        ? `<span class="card-badge badge-tag-green">Veg</span>`
                        : ``;

                    catHtml += `
                    <div class="menu-item-card">
                        <div class="card-img-container">
                            ${imgHtml}
                            ${badgeHtml}
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">${item.name}</h3>
                            <p class="card-desc">${item.description}</p>
                            <div class="card-actions">
                                <div class="card-price">
                                    ৳${parseFloat(item.price).toFixed(2)}
                                </div>
                                <button class="btn-add-to-cart" onclick="addToCart(${item.id}, event)">
                                    <span>Add</span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                </button>
                            </div>
                        </div>
                    </div>`;
                });
                
                catHtml += `</div></div>`;
                container.insertAdjacentHTML('beforeend', catHtml);
            }
        }
    } catch (e) {
        console.error("AJAX Search failed:", e);
    }
}

/**
 * Modern cart API dispatcher adding full-width tap response.
 */
async function addToCart(id, event) {
    // Add visual click animation feedback directly to the tapped button
    const btn = event.currentTarget;
    const originalContent = btn.innerHTML;
    
    btn.disabled = true;
    btn.style.background = '#10b981'; // Turn green temporarily to show success
    btn.innerHTML = `
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
        <span>Added</span>
    `;

    const result = await fetchJson('<?php echo url("/api/cart/add"); ?>', { id: id });
    
    if (result.success) {
        // Sync cart indicator badge
        document.getElementById('cart-count').textContent = result.cart_count;
        
        // Fast reload to seamlessly update PHP session data and floating mobile checkout components
        setTimeout(() => {
            location.reload();
        }, 300);
    } else {
        btn.disabled = false;
        btn.style.background = 'var(--brand-red)';
        btn.innerHTML = originalContent;
        if (result.unauthorized && result.redirect) {
            window.location.href = result.redirect;
        } else {
            alert(result.error || 'Failed to add item to cart');
        }
    }
}
</script>

<?php require 'views/layouts/footer.php'; ?>
