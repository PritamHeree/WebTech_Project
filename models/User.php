<?php
class User {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        // db
        // auth
    }
    
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function findByRememberToken($token) {
        // remember
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE remember_token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
    
    public function create($name, $email, $password, $address) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        // hash pass
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, delivery_address, role) VALUES (?, ?, ?, ?, 'customer')");
        return $stmt->execute([$name, $email, $hash, $address]);
    }
    
    public function updateRememberToken($userId, $token) {
        // hash pass
        // db
        $stmt = $this->pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
        return $stmt->execute([$token, $userId]);
    }
    
    public function updateProfile($id, $name, $email, $address) {
        $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ?, delivery_address = ? WHERE id = ?");
        return $stmt->execute([$name, $email, $address, $id]);
    }
    
    public function updatePassword($id, $password) {
        // hash pass
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }
}
