<?php
class Order {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        // dependency injection of the PDO instance keeps this model reusable
        // and avoids hardcoding database connections inside the class.
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
        return $stmt->fetchAll();
    }
    
    public function getByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($userId, $totalAmount, $address) {
        // new orders always start in Pending state
        // this is the first step in the order lifecycle
        $stmt = $this->pdo->prepare("INSERT INTO orders (user_id, total_amount, status, delivery_address) VALUES (?, ?, 'Pending', ?)");
        $stmt->execute([$userId, $totalAmount, $address]);
        return $this->pdo->lastInsertId();
    }
    
    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function getFiltered($statusFilter, $dateFilter) {
        // allow optional filtering for admin order views
        // this avoids loading too many rows when the admin only wants a subset
        $query = "SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE 1=1";
        $params = [];
        
        if ($statusFilter) {
            $query .= " AND o.status = ?";
            $params[] = $statusFilter;
        }
        
        if ($dateFilter) {
            $query .= " AND DATE(o.created_at) = ?";
            $params[] = $dateFilter;
        }
        
        $query .= " ORDER BY o.created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
