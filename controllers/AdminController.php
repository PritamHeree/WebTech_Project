<?php
class AdminController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        // admin-only controller guard: enforce both login and role in one place
        // this keeps every admin route protected without repeating checks later
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            redirect('/login');
        }
    }
    
    public function dashboard() {
        $catModel = new Category($this->pdo);
        $menuModel = new MenuItem($this->pdo);
        
        // show quick admin stats for the dashboard so the owner can spot issues fast
        $categoriesCount = $catModel->getCount();
        $menuItemsCount = $menuModel->getCount();
        $unavailableCount = $menuModel->getUnavailableCount();
        
        require 'views/admin/dashboard.php';
    }
    
    public function categories() {
        $catModel = new Category($this->pdo);
        $categories = $catModel->getAll();
        require 'views/admin/categories.php';
    }
    
    public function manageCategoriesPost() {
        $action = $_POST['action'];
        $catModel = new Category($this->pdo);
        
        if ($action === 'create') {
            $name = trim($_POST['name']);
            if (!empty($name)) {
                // create a new menu category; keep it simple and avoid empty names
                $catModel->create($name);
                $_SESSION['success'] = "Category created.";
            } else {
                $_SESSION['error'] = "Category name cannot be empty.";
                $_SESSION['old'] = $_POST;
            }
        } elseif ($action === 'edit') {
            $id = $_POST['id'];
            $name = trim($_POST['name']);
            if (!empty($name)) {
                // rename category safely, keeping the same ID
                $catModel->update($id, $name);
                $_SESSION['success'] = "Category updated.";
            } else {
                $_SESSION['error'] = "Category name cannot be empty.";
                $_SESSION['old'] = $_POST;
            }
        } elseif ($action === 'delete') {
            $id = $_POST['id'];
            // prevent deleting categories that still have menu items
            // this avoids leaving child records orphaned in the menu_items table
            if (!$catModel->delete($id)) {
                $_SESSION['error'] = "Cannot delete category: It has existing menu items.";
            } else {
                $_SESSION['success'] = "Category deleted.";
            }
        }
        redirect('/admin/categories');
    }
    
    public function menuItems() {
        $catModel = new Category($this->pdo);
        $categories = $catModel->getAll();
        
        $menuModel = new MenuItem($this->pdo);
        $menuItems = $menuModel->getAll();
        
        require 'views/admin/menu_items.php';
    }
    
    public function manageMenuItemsPost() {
        $action = $_POST['action'];
        $menuModel = new MenuItem($this->pdo);
        
        if ($action === 'create' || $action === 'edit') {
            // use a single handler for both create and edit to reduce duplicated logic
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $price = floatval($_POST['price']); // normalize form input to a float
            $categoryId = $_POST['category_id'];
            $isAvailable = isset($_POST['is_available']) ? 1 : 0; // checkbox => 1/0
            
            // basic validation before saving menu items
            if ($price <= 0) {
                $_SESSION['error'] = "Price must be positive.";
                $_SESSION['old'] = $_POST;
                redirect('/admin/menu-items');
            }
            
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                // validate uploaded image before moving it
                $tmpName = $_FILES['image']['tmp_name'];
                $size = $_FILES['image']['size'];
                $type = mime_content_type($tmpName);
                
                if ($size > 2000000) {
                    $_SESSION['error'] = "Image size exceeds 2MB.";
                    $_SESSION['old'] = $_POST;
                    redirect('/admin/menu-items');
                }
                if (!in_array($type, ['image/jpeg', 'image/png', 'image/webp'])) {
                    $_SESSION['error'] = "Only JPEG/PNG/WEBP images are allowed.";
                    $_SESSION['old'] = $_POST;
                    redirect('/admin/menu-items');
                }
                
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $ext;
                $dir = 'public/uploads/menu';
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $destination = $dir . '/' . $filename;
                
                if (move_uploaded_file($tmpName, $destination)) {
                    // only accept the upload if PHP has moved it securely
                    $imagePath = $destination;
                } else {
                    $_SESSION['error'] = "Failed to save the uploaded image.";
                    $_SESSION['old'] = $_POST;
                    redirect('/admin/menu-items');
                }
            } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $_SESSION['error'] = "Image upload failed with error code: " . $_FILES['image']['error'];
                $_SESSION['old'] = $_POST;
                redirect('/admin/menu-items');
            }
            
            if ($action === 'create') {
                // insert new menu item
                $menuModel->create($categoryId, $name, $description, $price, $imagePath, $isAvailable);
                $_SESSION['success'] = "Menu item created.";
            } else {
                $id = $_POST['id'];
                // update existing item, image is optional
                $menuModel->update($id, $categoryId, $name, $description, $price, $imagePath, $isAvailable);
                $_SESSION['success'] = "Menu item updated.";
            }
        } elseif ($action === 'delete') {
            $id = $_POST['id'];
            // remove file from disk if present when deleting a menu item
            $item = $menuModel->findById($id);
            if ($item && $item['image_path'] && file_exists($item['image_path'])) {
                unlink($item['image_path']);
            }
            $menuModel->delete($id);
            $_SESSION['success'] = "Menu item deleted.";
        }
        
        redirect('/admin/menu-items');
    }
    
    public function orders() {
        $orderModel = new Order($this->pdo);
        
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
        $dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
        
        // support admin filtering by status and date
        $orders = $orderModel->getFiltered($statusFilter, $dateFilter);
        
        // Fetch items for each order
        $orderItemModel = new OrderItem($this->pdo);
        foreach ($orders as &$order) {
            $order['items'] = $orderItemModel->getByOrder($order['id']);
        }
        
        require 'views/admin/orders.php';
    }
}
