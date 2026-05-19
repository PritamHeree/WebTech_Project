<?php
class ShopController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        // this controller is stateless aside from session cart data,
        // so it only needs a PDO connection to read menu items.
    }
    
    public function menu() {
        $menuModel = new MenuItem($this->pdo);
        // show only customer-facing items; admin-hidden items stay out of the public menu
        $items = $menuModel->getAvailable();
        require 'views/shop/menu.php';
    }
    
    public function cart() {
        require 'views/shop/cart.php';
    }
    
    public function checkout() {
        // require login before checkout because anonymous orders are not supported yet
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login to checkout.";
            redirect('/login');
        }
        
        // don't allow checkout with an empty cart; this keeps the flow consistent
        if (empty($_SESSION['cart'])) {
            $_SESSION['error'] = "Your cart is empty.";
            redirect('/menu');
        }
        
        $userModel = new User($this->pdo);
        $user = $userModel->findById($_SESSION['user_id']);
        
        // calculate total from session cart items so checkout summary stays accurate
        // do not trust any client-side subtotal values here
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        require 'views/shop/checkout.php';
    }
    
    public function checkoutPost() {
        // verify login again before placing order; this is a safety check for direct POSTs
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }
        
        if (empty($_SESSION['cart'])) {
            $_SESSION['error'] = "Your cart is empty.";
            redirect('/menu');
        }
        
        $address = trim($_POST['address']);
        if (empty($address)) {
            // keep user input so form can be repopulated after validation errors
            $_SESSION['error'] = "Delivery address is required.";
            $_SESSION['old'] = $_POST;
            redirect('/checkout');
        }
        
        $paymentMethod = $_POST['payment_method'] ?? '';
        if ($paymentMethod !== 'Cash' && $paymentMethod !== 'Card') {
            // fail-safe server-side validation for payment selection
            $_SESSION['error'] = "Please select a valid payment method.";
            redirect('/checkout');
        }
        
        // re-check menu item availability on submit to avoid stale cart orders
        $total = 0;
        $menuModel = new MenuItem($this->pdo);
        foreach ($_SESSION['cart'] as $id => $item) {
            $dbItem = $menuModel->findById($id);
            if (!$dbItem) {
                $_SESSION['error'] = "Some items in your cart are no longer available.";
                redirect('/cart');
            }
            // Optional: check availability
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
            // persist each cart line item with the price at checkout time
            $orderItemModel->create($orderId, $id, $item['quantity'], $item['price']);
        }
        
        // clear cart after successful order so duplicate submissions don't reuse the same items
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
