<?php
class OrderItem {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        
    }
    
    public function getByOrder($orderId) {
        
        $stmt = $this->pdo->prepare("SELECT oi.*, m.name FROM order_items oi JOIN menu_items m ON oi.menu_item_id = m.id WHERE oi.order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
    
    public function create($orderId, $menuItemId, $quantity, $price) {
        $stmt = $this->pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$orderId, $menuItemId, $quantity, $price]);
    }
}
