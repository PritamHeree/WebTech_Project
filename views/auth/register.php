<?php require 'views/layouts/header.php'; ?>
<div class="card" style="max-width: 500px; margin: 0 auto;">
    <h2>Register</h2>
    <form action="<?php echo url('/register'); ?>" method="POST">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" required value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Password (min 8 chars)</label>
            <input type="password" name="password" required minlength="8">
        </div>
        <?php // client-side hint for minimum password length ?>
        <div class="form-group">
            <label>Delivery Address</label>
            <textarea name="address" required rows="3"><?= htmlspecialchars($_SESSION['old']['address'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn">Register</button>
    </form>
    <?php unset($_SESSION['old']); // clear registration state after render ?>
</div>
<?php require 'views/layouts/footer.php'; ?>
