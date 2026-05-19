<?php require 'views/layouts/header.php'; ?>
<h2>Manage Categories</h2>

<div class="card">
    <h3>Add Category</h3>
    <form action="<?php echo url('/admin/categories'); ?>" method="POST" style="display: flex; gap: 1rem;">
        <input type="hidden" name="action" value="create">
        <input type="text" name="name" required placeholder="Category Name" style="flex: 1; padding: 0.5rem;" value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>">
        <?php // show any typed category name again after form error ?>
        <button type="submit" class="btn">Add</button>
    </form>
    <?php unset($_SESSION['old']); ?>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= $cat['id'] ?></td>
                <td>
                    <form action="<?php echo url('/admin/categories'); ?>" method="POST" style="display: flex; gap: 0.5rem;">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                        <input type="text" name="name" value="<?= htmlspecialchars($cat['name']) ?>" required style="padding: 0.25rem;">
                        <button type="submit" class="btn" style="padding: 0.25rem 0.5rem;">Save</button>
                    </form>
                </td>
                <td>
                    <form action="<?php echo url('/admin/categories'); ?>" method="POST" onsubmit="return confirm('Delete this category?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                        <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem;">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require 'views/layouts/footer.php'; ?>
