<?php require 'views/layouts/header.php'; ?>
<h2>Admin Dashboard</h2>
<div class="grid">
    <div class="card" style="text-align: center;">
        <h3>Categories</h3>
        <p style="font-size: 2rem; font-weight: bold;"><?= $categoriesCount ?></p>
        <a href="<?php echo url('/admin/categories'); ?>" class="btn">Manage</a>
    </div>
    <div class="card" style="text-align: center;">
        <h3>Menu Items</h3>
        <p style="font-size: 2rem; font-weight: bold;"><?= $menuItemsCount ?></p>
        <a href="<?php echo url('/admin/menu-items'); ?>" class="btn">Manage</a>
    </div>
    <div class="card" style="text-align: center; border-left: 4px solid #dc3545;">
        <h3>Unavailable Items</h3>
        <p style="font-size: 2rem; font-weight: bold; color: #dc3545;"><?= $unavailableCount ?></p>
        <?php // highlight items hidden from customers so admin can act on them quickly ?>
    </div>
    <div class="card" style="text-align: center;">
        <h3>Orders Queue</h3>
        <p style="font-size: 2rem; font-weight: bold;">-</p>
        <a href="<?php echo url('/admin/orders'); ?>" class="btn">View Orders</a>
    </div>
</div>
<?php require 'views/layouts/footer.php'; ?>
