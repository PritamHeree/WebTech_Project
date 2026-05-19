<?php require 'views/layouts/header.php'; ?>

<div class="auth-container" style="max-width: 520px;">
    <div class="auth-card">
        <h2 class="auth-title">Create Account</h2>
        <p class="auth-subtitle">Join us today to get fresh pizza delivered fast</p>
        
        <form action="<?php echo url('/register'); ?>" method="POST">
            <div class="form-group">
                <label class="form-label-custom">Full Name</label>
                <input type="text" name="name" class="form-input-custom" required value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>" placeholder="John Doe">
            </div>
            
            <div class="form-group">
                <label class="form-label-custom">Email Address</label>
                <input type="email" name="email" class="form-input-custom" required value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>" placeholder="john@example.com">
            </div>
            
            <div class="form-group">
                <label class="form-label-custom">Password</label>
                <input type="password" name="password" class="form-input-custom" required minlength="8" placeholder="Minimum 8 characters">
            </div>
            
            <div class="form-group">
                <label class="form-label-custom">Delivery Address</label>
                <textarea name="address" class="form-input-custom" required rows="3" placeholder="Enter your full street address, apartment number, and city..." style="resize: vertical; min-height: 80px; font-family: inherit; line-height: 1.5;"><?= htmlspecialchars($_SESSION['old']['address'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" class="btn-auth-submit" style="margin-top: var(--spacing-md);">Register Account</button>
        </form>
        
        <div class="auth-footer-text">
            Already have an account? 
            <a href="<?php echo url('/login'); ?>" class="auth-footer-link">Sign In</a>
        </div>
    </div>
</div>

<?php 
unset($_SESSION['old']); 
require 'views/layouts/footer.php'; 
?>
