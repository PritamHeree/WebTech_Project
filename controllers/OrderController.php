<?php
class OrderController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;

        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }
    }
    
    public function myOrders() {
        $orderModel = new Order($this->pdo);
        $orders = $orderModel->getByUser($_SESSION['user_id']);

        $orderItemModel = new OrderItem($this->pdo);
        foreach ($orders as &$order) {
            $order['items'] = $orderItemModel->getByOrder($order['id']);
        }
        
        require 'views/user/orders.php';
    }
}
