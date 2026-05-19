<?php
class UserController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;

        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }
    }
    
    public function profile() {
        $userModel = new User($this->pdo);
        $user = $userModel->findById($_SESSION['user_id']);
        require 'views/user/profile.php';
    }
    
    public function updateProfile() {
        $userModel = new User($this->pdo);
        $id = $_SESSION['user_id'];
        $user = $userModel->findById($id);
        
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $address = trim($_POST['address']);
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];

        if (!password_verify($currentPassword, $user['password'])) {
            $_SESSION['error'] = "Current password is incorrect.";
            $_SESSION['old'] = $_POST;
            redirect('/profile');
        }
        
        $userModel->updateProfile($id, $name, $email, $address);
        
        $_SESSION['name'] = $name; 
        
        if (!empty($newPassword)) {
            
            if (strlen($newPassword) < 8) {
                $_SESSION['error'] = "New password must be at least 8 characters.";
                $_SESSION['old'] = $_POST;
                redirect('/profile');
            }
            $userModel->updatePassword($id, $newPassword);
        }
        
        $_SESSION['success'] = "Profile updated successfully.";
        redirect('/profile');
    }
}
