<?php require 'views/layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <h2 class="auth-title">Verify Email</h2>
        <p class="auth-subtitle">We sent a 6-digit verification code to your email address</p>
        
        <form action="<?php echo url('/verify'); ?>" method="POST">
            <div class="form-group">
                <label class="form-label-custom">Verification Code</label>
                <input type="text" name="code" class="form-input-custom" required pattern="[0-9]{6}" maxlength="6" placeholder="123456" style="text-align: center; font-size: 1.4rem; letter-spacing: 6px; padding: var(--spacing-sm) 0;">
                <span style="display: block; font-size: 0.75rem; color: var(--color-text-muted); margin-top: var(--spacing-xs); text-align: center;">Enter the 6-digit code sent to your email.</span>
            </div>
            
            <button type="submit" class="btn-auth-submit" style="margin-top: var(--spacing-md);">Verify Code</button>
        </form>
        
        <div class="auth-footer-text">
            Didn't receive the code? 
            <a href="<?php echo url('/register'); ?>" class="auth-footer-link">Try Registering Again</a>
        </div>
    </div>
</div>

<?php require 'views/layouts/footer.php'; ?>
