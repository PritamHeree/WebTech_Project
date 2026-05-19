<?php require 'views/layouts/header.php'; ?>

<div class="auth-container" style="max-width: 640px; margin: 3rem auto;">
    <div class="auth-card" style="padding: var(--spacing-xl);">
        <h2 class="auth-title" style="text-align: left; border-bottom: 3px solid var(--brand-red); padding-bottom: var(--spacing-xs); margin-bottom: var(--spacing-lg);">My Profile</h2>
        
        <form action="<?php echo url('/profile'); ?>" method="POST">
            <div class="form-group">
                <label class="form-label-custom">Name</label>
                <input type="text" name="name" class="form-input-custom" required value="<?= htmlspecialchars($_SESSION['old']['name'] ?? $user['name']) ?>" placeholder="Your Name">
            </div>
            
            <div class="form-group">
                <label class="form-label-custom">Email Address</label>
                <input type="email" name="email" class="form-input-custom" required value="<?= htmlspecialchars($_SESSION['old']['email'] ?? $user['email']) ?>" placeholder="yourname@domain.com">
            </div>
            
            <div class="form-group">
                <label class="form-label-custom">Default Delivery Address</label>
                <textarea name="address" class="form-input-custom" required rows="3" placeholder="Enter your full shipping address..." style="resize: vertical; min-height: 80px; font-family: inherit; line-height: 1.5;"><?= htmlspecialchars($_SESSION['old']['address'] ?? $user['delivery_address'] ?? '') ?></textarea>
            </div>
            
            <div style="border: 0; border-top: 1.5px solid var(--color-border); margin: 2rem 0; height: 0;"></div>
            
            <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--brand-blue); margin: 0 0 var(--spacing-md) 0;">Update Password</h3>
            
            <div class="form-group">
                <label class="form-label-custom">Current Password (Required to save any changes)</label>
                <input type="password" name="current_password" class="form-input-custom" required placeholder="••••••••">
            </div>
            
            <div class="form-group">
                <label class="form-label-custom">New Password (Leave blank to keep current password)</label>
                <input type="password" name="new_password" class="form-input-custom" placeholder="Minimum 8 characters">
            </div>
            
            <button type="submit" class="btn-auth-submit" style="margin-top: var(--spacing-lg); background: var(--brand-orange); box-shadow: 0 4px 12px rgba(234, 88, 12, 0.2);">Save Profile Changes</button>
        </form>
    </div>
</div>

<?php 
unset($_SESSION['old']); 
require 'views/layouts/footer.php'; 
?>
