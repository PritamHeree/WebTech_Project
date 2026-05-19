<?php require 'views/layouts/header.php'; ?>
<div class="card" style="max-width: 400px; margin: 0 auto;">
    <h2>Login</h2>
    <form action="<?php echo url('/login'); ?>" method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>">
        </div>
        <?php // restore entered email after failed login attempt ?>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="remember"> Remember Me
            </label>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
    <?php unset($_SESSION['old']); // clear preserved form state after rendering ?>
</div>
<?php require 'views/layouts/footer.php'; ?>
