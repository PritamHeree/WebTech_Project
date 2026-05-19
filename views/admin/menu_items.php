<?php require 'views/layouts/header.php'; ?>
<h2>Manage Menu Items</h2>

<div class="card">
    <h3 id="form-title">Add Menu Item</h3>
    <?php ?>
    <form action="<?php echo url('/admin/menu-items'); ?>" method="POST" enctype="multipart/form-data" id="item-form">
        <input type="hidden" name="action" id="form-action" value="<?= htmlspecialchars($_SESSION['old']['action'] ?? 'create') ?>">
        <input type="hidden" name="id" id="item-id" value="<?= htmlspecialchars($_SESSION['old']['id'] ?? '') ?>">
        <div class="grid">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" id="item-name" required value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" id="item-category" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (($_SESSION['old']['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Price</label>
                <input type="number" step="0.01" name="price" id="item-price" required min="0.01" value="<?= htmlspecialchars($_SESSION['old']['price'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Image (JPEG/PNG/WEBP < 2MB)</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
            </div>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" id="item-description" rows="2"><?= htmlspecialchars($_SESSION['old']['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_available" id="item-available" <?= (isset($_SESSION['old']) ? (isset($_SESSION['old']['is_available']) ? 'checked' : '') : 'checked') ?>> Available
            </label>
        </div>
        <button type="submit" class="btn" id="form-submit-btn">Add Item</button>
    </form>
    <?php unset($_SESSION['old']); ?>
</div>

<div class="card">
    <table style="font-size: 0.9rem;">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menuItems as $item): ?>
            <tr>
                <td>
                    <?php if ($item['image_path']): ?>
                        <img src="<?php echo url('/' . htmlspecialchars($item['image_path'])); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover;">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= htmlspecialchars($item['category_name']) ?></td>
                <td>৳<?= number_format($item['price'], 2) ?></td>
                <td>
                    <button type="button" class="badge <?= $item['is_available'] ? 'badge-active' : 'badge-inactive' ?>" style="border:none; cursor:pointer;" onclick="toggleStatus(<?= $item['id'] ?>, <?= $item['is_available'] ? 0 : 1 ?>, this)">
                        <?= $item['is_available'] ? 'Active' : 'Inactive' ?>
                    </button>
                </td>
                <td>
                    
                    <button type="button" class="btn" style="padding: 0.25rem 0.5rem;" onclick='editItem(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>Edit</button>
                    
                    <form action="<?php echo url('/admin/menu-items'); ?>" method="POST" onsubmit="return confirm('Delete this item?');" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem;">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
async function toggleStatus(id, newStatus, btnElement) {
    const result = await fetchJson('<?php echo url("/api/menu-items/toggle"); ?>', { id: id, status: newStatus });
    if (result.ok) {
        if (result.is_available) {
            btnElement.className = 'badge badge-active';
            btnElement.textContent = 'Active';
            btnElement.setAttribute('onclick', `toggleStatus(${id}, 0, this)`);
        } else {
            btnElement.className = 'badge badge-inactive';
            btnElement.textContent = 'Inactive';
            btnElement.setAttribute('onclick', `toggleStatus(${id}, 1, this)`);
        }
    } else {
        alert(result.error || 'Failed to update status');
    }
}

function editItem(item) {
    document.getElementById('form-title').textContent = 'Edit Menu Item: ' + item.name;
    document.getElementById('form-action').value = 'edit';
    document.getElementById('item-id').value = item.id;
    document.getElementById('item-name').value = item.name;
    document.getElementById('item-category').value = item.category_id;
    document.getElementById('item-price').value = item.price;
    document.getElementById('item-description').value = item.description || '';
    document.getElementById('item-available').checked = parseInt(item.is_available) === 1;
    document.getElementById('form-submit-btn').textContent = 'Save Changes';

    if (!document.getElementById('btn-cancel')) {
        const cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.id = 'btn-cancel';
        cancelBtn.className = 'btn';
        cancelBtn.style.background = '#6c757d';
        cancelBtn.style.marginLeft = '0.5rem';
        cancelBtn.textContent = 'Cancel';
        cancelBtn.onclick = resetForm;
        document.getElementById('form-submit-btn').after(cancelBtn);
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('form-title').textContent = 'Add Menu Item';
    document.getElementById('form-action').value = 'create';
    document.getElementById('item-id').value = '';
    document.getElementById('item-form').reset();
    document.getElementById('form-submit-btn').textContent = 'Add Item';
    const cancelBtn = document.getElementById('btn-cancel');
    if (cancelBtn) cancelBtn.remove();
}
</script>
<?php require 'views/layouts/footer.php'; ?>
