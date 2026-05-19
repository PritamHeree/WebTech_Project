<?php
class ShopController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;

    }
    
    public function menu() {
        $menuModel = new MenuItem($this->pdo);
        
        $items = $menuModel->getAvailable();
        require 'views/shop/menu.php';
    }
    
    public function cart() {
        require 'views/shop/cart.php';
    }
    
    public function checkout() {
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login to checkout.";
            redirect('/login');
        }

        if (empty($_SESSION['cart'])) {
            $_SESSION['error'] = "Your cart is empty.";
            redirect('/menu');
        }
        
        $userModel = new User($this->pdo);
        $user = $userModel->findById($_SESSION['user_id']);

        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        require 'views/shop/checkout.php';
    }
    
    public function checkoutPost() {
        
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }
        
        if (empty($_SESSION['cart'])) {
            $_SESSION['error'] = "Your cart is empty.";
            redirect('/menu');
        }
        
        $address = trim($_POST['address']);
        if (empty($address)) {
            
            $_SESSION['error'] = "Delivery address is required.";
            $_SESSION['old'] = $_POST;
            redirect('/checkout');
        }
        
        $paymentMethod = $_POST['payment_method'] ?? '';
        if ($paymentMethod !== 'Cash' && $paymentMethod !== 'Card') {
            
            $_SESSION['error'] = "Please select a valid payment method.";
            redirect('/checkout');
        }

        $total = 0;
        $menuModel = new MenuItem($this->pdo);
        foreach ($_SESSION['cart'] as $id => $item) {
            $dbItem = $menuModel->findById($id);
            if (!$dbItem) {
                $_SESSION['error'] = "Some items in your cart are no longer available.";
                redirect('/cart');
            }
            
            if (!$dbItem['is_available']) {
                $_SESSION['error'] = "Item '" . $item['name'] . "' is currently unavailable.";
                redirect('/cart');
            }
            $total += $item['price'] * $item['quantity'];
        }
        
        $orderModel = new Order($this->pdo);
        $orderId = $orderModel->create($_SESSION['user_id'], $total, $address);
        
        $orderItemModel = new OrderItem($this->pdo);
        foreach ($_SESSION['cart'] as $id => $item) {
            
            $orderItemModel->create($orderId, $id, $item['quantity'], $item['price']);
        }

        unset($_SESSION['cart']);
        
        $_SESSION['success'] = "Order placed successfully!";
        redirect('/confirmation?id=' . $orderId);
    }
    
    public function confirmation() {
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }
        
        $orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $orderModel = new Order($this->pdo);
        $order = $orderModel->findById($orderId);
        
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Order not found.";
            redirect('/');
        }
        
        $orderItemModel = new OrderItem($this->pdo);
        $items = $orderItemModel->getByOrder($orderId);
        
        require 'views/shop/confirmation.php';
    }
}
