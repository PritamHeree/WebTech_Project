<?php
class AuthController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function login() {
        
        if (isset($_SESSION['user_id'])) {
            redirect('/');
        }
        require 'views/auth/login.php';
    }
    
    public function loginPost() {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']);
        
        $userModel = new User($this->pdo);
        $user = $userModel->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            
            if ($remember) {

                $token = bin2hex(random_bytes(32));
                $hash = hash('sha256', $token);
                $userModel->updateRememberToken($user['id'], $hash);
                setcookie('remember_token', $token, time() + (86400 * 30), "/"); 
            }
            
            if ($user['role'] === 'admin') {
                redirect('/admin');
            } else {
                redirect('/');
            }
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            $_SESSION['old'] = ['email' => $email];
            redirect('/login');
        }
    }
    
    public function register() {
        if (isset($_SESSION['user_id'])) {
            redirect('/');
        }
        require 'views/auth/register.php';
    }
    
    public function registerPost() {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $address = trim($_POST['address']);
        
        $userModel = new User($this->pdo);
        
        if (strlen($password) < 8) {
            $_SESSION['error'] = "Password must be at least 8 characters.";
            $_SESSION['old'] = $_POST;
            redirect('/register');
        }
        
        if ($userModel->findByEmail($email)) {
            $_SESSION['error'] = "Email already in use.";
            $_SESSION['old'] = $_POST;
            redirect('/register');
        }
        
        $code = rand(100000, 999999);
        
        $_SESSION['temp_register'] = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'address' => $address,
            'code' => $code,
            'expires' => time() + 600
        ];
        
        $subject = "Verify Your Account - KhudaLagse?";
        $htmlBody = "
            <div style='font-family: sans-serif; max-width: 500px; margin: 0 auto; padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.05);'>
                <h2 style='color: #002855; font-size: 1.6rem; font-weight: 800; margin: 0; line-height: 1.1;'>KhudaLagse?</h2>
                <p style='color: #64748b; font-size: 0.8rem; margin: 4px 0 0 0;'>Hungry? We're on our way.</p>
                <div style='border: 0; border-top: 1.5px solid #e2e8f0; margin: 20px 0;'></div>
                <p style='font-size: 1rem; color: #0f172a; line-height: 1.5; margin: 0 0 16px 0;'>Hello <strong>" . htmlspecialchars($name) . "</strong>,</p>
                <p style='font-size: 0.95rem; color: #64748b; line-height: 1.5; margin: 0 0 24px 0;'>Thank you for registering at KhudaLagse?. Use the following 6-digit code to verify your email address:</p>
                <div style='background: #f1f5f9; padding: 16px; border-radius: 8px; text-align: center; font-size: 1.8rem; font-weight: 800; color: #e31837; letter-spacing: 6px; margin-bottom: 24px;'>
                    " . $code . "
                </div>
                <p style='font-size: 0.8rem; color: #94a3b8; line-height: 1.5; margin: 0;'>This code will expire in 10 minutes. If you did not request this code, you can safely ignore this email.</p>
            </div>
        ";
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: KhudaLagse? <no-reply@khudalagse.com>\r\n";
        
        @mail($email, $subject, $htmlBody, $headers);
        
        $_SESSION['success'] = "Verification code generated! (Sandbox code: " . $code . ")";
        redirect('/verify');
    }
    
    public function verify() {
        if (!isset($_SESSION['temp_register'])) {
            redirect('/register');
        }
        require 'views/auth/verify.php';
    }
    
    public function verifyPost() {
        if (!isset($_SESSION['temp_register'])) {
            $_SESSION['error'] = "Verification session expired. Please register again.";
            redirect('/register');
        }
        
        $temp = $_SESSION['temp_register'];
        
        if (time() > $temp['expires']) {
            unset($_SESSION['temp_register']);
            $_SESSION['error'] = "Verification code expired. Please register again.";
            redirect('/register');
        }
        
        $enteredCode = trim($_POST['code']);
        
        if (strval($enteredCode) === strval($temp['code'])) {
            $userModel = new User($this->pdo);
            
            if ($userModel->create($temp['name'], $temp['email'], $temp['password'], $temp['address'])) {
                $user = $userModel->findByEmail($temp['email']);
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];
                }
                
                unset($_SESSION['temp_register']);
                $_SESSION['success'] = "Account verified successfully! Welcome to KhudaLagse?";
                redirect('/');
            } else {
                $_SESSION['error'] = "Registration failed. Please try again.";
                redirect('/verify');
            }
        } else {
            $_SESSION['error'] = "Invalid verification code. Please try again.";
            redirect('/verify');
        }
    }
    
    public function logout() {

        session_destroy();
        if (isset($_COOKIE['remember_token'])) {
            unset($_COOKIE['remember_token']);
            setcookie('remember_token', '', -1, '/');
        }
        redirect('/login');
    }
}
