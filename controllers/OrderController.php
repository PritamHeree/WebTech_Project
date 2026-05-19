<?php
class OrderController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        // order pages
        // auth
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }
    }
    
    public function myOrders() {
        $orderModel = new Order($this->pdo);
        $orders = $orderModel->getByUser($_SESSION['user_id']);
        
        // attach line
        $orderItemModel = new OrderItem($this->pdo);
        foreach ($orders as &$order) {
            $order['items'] = $orderItemModel->getByOrder($order['id']);
        }
        
        require 'views/user/orders.php';
    }
}
