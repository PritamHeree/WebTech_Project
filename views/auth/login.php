<?php require 'views/layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <h2 class="auth-title">Welcome Back</h2>
        <p class="auth-subtitle">Sign in to your account to place your order</p>
        
        <form action="<?php echo url('/login'); ?>" method="POST">
            <div class="form-group">
                <label class="form-label-custom">Email Address</label>
                <input type="email" name="email" class="form-input-custom" required value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>" placeholder="yourname@domain.com">
            </div>
            
            <div class="form-group">
                <label class="form-label-custom">Password</label>
                <input type="password" name="password" class="form-input-custom" required placeholder="••••••••">
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; justify-content: space-between; margin-top: var(--spacing-sm); margin-bottom: var(--spacing-lg);">
                <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 500; color: var(--color-text-muted); cursor: pointer; user-select: none;">
                    <input type="checkbox" name="remember" style="width: 16px; height: 16px; accent-color: var(--brand-blue); cursor: pointer;"> 
                    Remember Me
                </label>
            </div>
            
            <button type="submit" class="btn-auth-submit">Sign In</button>
        </form>
        
        <div class="auth-footer-text">
            Don't have an account? 
            <a href="<?php echo url('/register'); ?>" class="auth-footer-link">Create Account</a>
        </div>
    </div>
</div>

<?php 
unset($_SESSION['old']); 
require 'views/layouts/footer.php'; 
?>
