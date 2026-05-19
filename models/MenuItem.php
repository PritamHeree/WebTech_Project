<?php
class MenuItem {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT m.*, c.name as category_name FROM menu_items m LEFT JOIN categories c ON m.category_id = c.id ORDER BY m.id DESC");
        return $stmt->fetchAll();
    }
    
    public function getAvailable() {

        $stmt = $this->pdo->query("SELECT m.*, c.name as category_name FROM menu_items m LEFT JOIN categories c ON m.category_id = c.id WHERE m.is_available = 1 ORDER BY c.name ASC, m.name ASC");
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($category_id, $name, $description, $price, $image_path, $is_available) {
        $stmt = $this->pdo->prepare("INSERT INTO menu_items (category_id, name, description, price, image_path, is_available) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$category_id, $name, $description, $price, $image_path, $is_available]);
    }
    
    public function update($id, $category_id, $name, $description, $price, $image_path, $is_available) {

        if ($image_path) {
            $stmt = $this->pdo->prepare("UPDATE menu_items SET category_id = ?, name = ?, description = ?, price = ?, image_path = ?, is_available = ? WHERE id = ?");
            return $stmt->execute([$category_id, $name, $description, $price, $image_path, $is_available, $id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE menu_items SET category_id = ?, name = ?, description = ?, price = ?, is_available = ? WHERE id = ?");
            return $stmt->execute([$category_id, $name, $description, $price, $is_available, $id]);
        }
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM menu_items WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function toggleAvailability($id, $status) {
        
        $stmt = $this->pdo->prepare("UPDATE menu_items SET is_available = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function getCount() {
        return $this->pdo->query("SELECT COUNT(*) FROM menu_items")->fetchColumn();
    }

    public function getUnavailableCount() {
        return $this->pdo->query("SELECT COUNT(*) FROM menu_items WHERE is_available = 0")->fetchColumn();
    }
}
