<?php require 'views/layouts/header.php'; ?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2>My Profile</h2>
    <form action="<?php echo url('/profile'); ?>" method="POST">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" required value="<?= htmlspecialchars($_SESSION['old']['name'] ?? $user['name']) ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($_SESSION['old']['email'] ?? $user['email']) ?>">
        </div>
        <div class="form-group">
            <label>Delivery Address</label>
            <textarea name="address" required rows="3"><?= htmlspecialchars($_SESSION['old']['address'] ?? $user['delivery_address'] ?? '') ?></textarea>
        </div>
        <?php // keep old values after submission errors ?>
        <hr style="margin: 1rem 0;">
        <h3>Change Password</h3>
        <div class="form-group">
            <label>Current Password (required to save changes)</label>
            <input type="password" name="current_password" required>
        </div>
        <?php // require current password to prevent unauthorized profile edits ?>
        <div class="form-group">
            <label>New Password (leave blank to keep current)</label>
            <input type="password" name="new_password">
        </div>
        <button type="submit" class="btn">Update Profile</button>
    </form>
    <?php unset($_SESSION['old']); ?>
</div>
<?php require 'views/layouts/footer.php'; ?>
