<?php
class ApiController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        // make sure every API response is JSON; avoids confusing the client with HTML errors
        // and ensures the JavaScript client can parse every response consistently.
        header('Content-Type: application/json');
    }
    
    public function toggleMenuItem() {
        // API endpoint only for admin users; this is a strict server-side gate
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }
        
        $id = $_POST['id'];
        $status = $_POST['status'];
        
        $menuModel = new MenuItem($this->pdo);
        if ($menuModel->toggleAvailability($id, $status)) {
            echo json_encode(['ok' => true, 'is_available' => (bool)$status]);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Failed to update']);
        }
    }
    
    public function searchMenuItems() {
        $q = isset($_GET['q']) ? $_GET['q'] : '';
        $menuModel = new MenuItem($this->pdo);
        
        // keep search simple for now by filtering available items in PHP
        // this is okay for modest menu sizes; for large datasets we'd move this into SQL
        $items = $menuModel->getAvailable();
        $filtered = [];
        if ($q === '') {
            $filtered = $items;
        } else {
            foreach ($items as $item) {
                if (stripos($item['name'] ?? '', $q) !== false || stripos($item['category_name'] ?? '', $q) !== false || stripos($item['description'] ?? '', $q) !== false) {
                    $filtered[] = $item;
                }
            }
        }
        echo json_encode(['ok' => true, 'items' => $filtered]);
    }
    
    public function addToCart() {
        $id = $_POST['id'];
        
        $menuModel = new MenuItem($this->pdo);
        $item = $menuModel->findById($id);
        
        // verify availability here too, not just on the menu page
        // session/cart state is not authoritative without server-side confirmation
        if (!$item || !$item['is_available']) {
            echo json_encode(['success' => false, 'error' => 'Item not available']);
            return;
        }
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // keep cart quantities in the session so order flow works across pages
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => 1
            ];
        }
        
        $count = array_sum(array_column($_SESSION['cart'], 'quantity'));
        echo json_encode(['success' => true, 'cart_count' => $count]);
    }
    
    public function updateCartQuantity() {
        $id = $_POST['id'];
        $action = $_POST['action']; // 'increase' or 'decrease'
        
        if (isset($_SESSION['cart'][$id])) {
            if ($action === 'increase') {
                $_SESSION['cart'][$id]['quantity']++;
            } elseif ($action === 'decrease') {
                // decrement and remove items at zero quantity to keep session data clean
                $_SESSION['cart'][$id]['quantity']--;
                if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                    unset($_SESSION['cart'][$id]);
                }
            }
        }
        
        $this->returnCartTotals();
    }
    
    public function removeFromCart() {
        $id = $_POST['id'];
        
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        
        $this->returnCartTotals();
    }
    
    private function returnCartTotals() {
        // total and count are used by the UI to refresh cart summary after updates
        // this keeps the front-end consistent with the server-side session state
        $total = 0;
        $count = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['quantity'];
                $count += $item['quantity'];
            }
        }
        echo json_encode(['success' => true, 'total' => number_format($total, 2), 'cart_count' => $count]);
    }
    
    public function getOrderStatus($id) {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }
        
        $orderModel = new Order($this->pdo);
        $order = $orderModel->findById($id);
        
        if ($order && ($order['user_id'] == $_SESSION['user_id'] || $_SESSION['role'] === 'admin')) {
            echo json_encode(['success' => true, 'status' => $order['status']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Not found or unauthorized']);
        }
    }
    
    public function updateOrderStatus($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }
        
        // Parse PUT body (JSON or form-urlencoded)
        // support both request formats so frontend clients can send either type
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if (!$data) {
            parse_str($input, $data);
        }
        $status = $data['status'] ?? '';

        $validStatuses = ['Pending', 'Preparing', 'Out for Delivery', 'Delivered'];
        
        if (!in_array($status, $validStatuses)) {
            echo json_encode(['ok' => false, 'error' => 'Invalid status']);
            return;
        }
        
        $orderModel = new Order($this->pdo);
        $order = $orderModel->findById($id);
        
        if (!$order) {
            echo json_encode(['ok' => false, 'error' => 'Order not found']);
            return;
        }
        
        $currentStatus = $order['status'];
        $currentIndex = array_search($currentStatus, $validStatuses);
        $newIndex = array_search($status, $validStatuses);
        
        if ($newIndex !== $currentIndex + 1) {
            echo json_encode(['ok' => false, 'error' => 'Invalid status transition. Must follow sequential order.']);
            return;
        }
        
        if ($orderModel->updateStatus($id, $status)) {
            echo json_encode(['ok' => true]);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Failed to update status']);
        }
    }
}
