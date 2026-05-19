<?php
class AuthController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function login() {
        // already logged in? keep people out of the login page
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
            // success login: store minimal identity info in session
            // keeping role here avoids extra DB lookups on protected pages
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            
            if ($remember) {
                // store a hashed remember token in DB and raw token in cookie
                // raw token is only needed on the client; the DB keeps the hash for verification
                $token = bin2hex(random_bytes(32));
                $hash = hash('sha256', $token);
                $userModel->updateRememberToken($user['id'], $hash);
                setcookie('remember_token', $token, time() + (86400 * 30), "/"); // 30 days
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
        
        // Validation
        // enforce minimum password strength before hashing and storing
        if (strlen($password) < 8) {
            // prevent weak passwords at registration
            $_SESSION['error'] = "Password must be at least 8 characters.";
            $_SESSION['old'] = $_POST;
            redirect('/register');
        }
        
        if ($userModel->findByEmail($email)) {
            // enforce unique email addresses for login
            // this also avoids ambiguous authentication where multiple accounts share one email
            $_SESSION['error'] = "Email already in use.";
            $_SESSION['old'] = $_POST;
            redirect('/register');
        }
        
        if ($userModel->create($name, $email, $password, $address)) {
            $_SESSION['success'] = "Registration successful. Please login.";
            redirect('/login');
        } else {
            $_SESSION['error'] = "Registration failed.";
            $_SESSION['old'] = $_POST;
            redirect('/register');
        }
    }
    
    public function logout() {
        // clear session and persistent login token
        // session_destroy removes server-side session state
        session_destroy();
        if (isset($_COOKIE['remember_token'])) {
            unset($_COOKIE['remember_token']);
            setcookie('remember_token', '', -1, '/');
        }
        redirect('/login');
    }
}
