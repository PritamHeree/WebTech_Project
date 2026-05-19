<?php require 'views/layouts/header.php'; ?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h2>Our Menu</h2>
    <input type="text" id="search" placeholder="Search menu..." style="padding: 0.5rem; width: 250px; border: 1px solid #ccc; border-radius: 4px;" onkeyup="filterMenu()">
</div>

<div id="menu-container">
    <?php 
    // group items by category so the menu stays easier to scan
    $grouped = [];
    foreach ($items as $item) {
        $grouped[$item['category_name']][] = $item;
    }
    foreach ($grouped as $categoryName => $categoryItems):
    ?>
        <h3 class="category-title" style="margin-top: 2rem; border-bottom: 2px solid #ddd; padding-bottom: 0.5rem;"><?= htmlspecialchars($categoryName) ?></h3>
        <div class="grid">
            <?php foreach ($categoryItems as $item): ?>
                <div class="card menu-card" data-name="<?= strtolower(htmlspecialchars($item['name'])) ?>" data-cat="<?= strtolower(htmlspecialchars($item['category_name'])) ?>">
                    <?php if ($item['image_path']): ?>
                        <img src="<?php echo url('/' . htmlspecialchars($item['image_path'])); ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    <?php else: ?>
                        <!-- fallback for missing images so card layout stays intact -->
                        <div style="width: 100%; height: 200px; background: #eee; display: flex; align-items: center; justify-content: center; color: #aaa;">No Image</div>
                    <?php endif; ?>
                    
                    <div style="padding-top: 1rem;">
                        <h3 style="margin: 0 0 0.5rem 0;"><?= htmlspecialchars($item['name']) ?></h3>
                        <p style="color: #666; font-size: 0.9rem; height: 2.7rem; overflow: hidden;"><?= htmlspecialchars($item['description']) ?></p>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                            <strong style="font-size: 1.2rem;">৳<?= number_format($item['price'], 2) ?></strong>
                            <button class="btn btn-success" onclick="addToCart(<?= $item['id'] ?>)">Add to Cart</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

<script>
async function filterMenu() {
    const query = document.getElementById('search').value;
    try {
        // use API search so filtering is fast and we avoid full page reloads
        const response = await fetch('<?php echo url("/api/menu-items/search"); ?>?q=' + encodeURIComponent(query));
        const result = await response.json();
        
        if (result.ok) {
            const container = document.getElementById('menu-container');
            container.innerHTML = '';
            
            // Group in JS
            const grouped = {};
            result.items.forEach(item => {
                if (!grouped[item.category_name]) {
                    grouped[item.category_name] = [];
                }
                grouped[item.category_name].push(item);
            });
            
            if (Object.keys(grouped).length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #888; margin-top: 2rem;">No items found matching your search.</p>';
                return;
            }
            
            for (const catName in grouped) {
                let catHtml = `
                <h3 class="category-title" style="margin-top: 2rem; border-bottom: 2px solid #ddd; padding-bottom: 0.5rem;">${catName}</h3>
                <div class="grid">`;
                
                grouped[catName].forEach(item => {
                    const imgHtml = item.image_path 
                        ? `<img src="<?php echo url('/'); ?>${item.image_path}" alt="${item.name}">`
                        : `<div style="width: 100%; height: 200px; background: #eee; display: flex; align-items: center; justify-content: center; color: #aaa;">No Image</div>`;
                    
                    catHtml += `
                    <div class="card menu-card">
                        ${imgHtml}
                        <div style="padding-top: 1rem;">
                            <h3 style="margin: 0 0 0.5rem 0;">${item.name}</h3>
                            <p style="color: #666; font-size: 0.9rem; height: 2.7rem; overflow: hidden;">${item.description}</p>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                                <strong style="font-size: 1.2rem;">৳${parseFloat(item.price).toFixed(2)}</strong>
                                <button class="btn btn-success" onclick="addToCart(${item.id})">Add to Cart</button>
                            </div>
                        </div>
                    </div>`;
                });
                
                catHtml += `</div>`;
                container.insertAdjacentHTML('beforeend', catHtml);
            }
        }
    } catch (e) {
        console.error("Search failed:", e);
    }
}

async function addToCart(id) {
    const result = await fetchJson('<?php echo url("/api/cart/add"); ?>', { id: id });
    if (result.success) {
        document.getElementById('cart-count').textContent = result.cart_count;
    } else {
        alert(result.error || 'Failed to add item');
    }
}
</script>
<?php require 'views/layouts/footer.php'; ?>
