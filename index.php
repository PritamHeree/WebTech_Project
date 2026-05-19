<?php
session_start();
require_once 'config/database.php';

// Start each request with a shared database connection and session state
// This file is the single entry point and orchestrates controller dispatch.

// Helper for generating base URLs
function url($path = '') {
    $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    if ($basePath === '/' || $basePath === '.') {
        $basePath = '';
    }
    $basePath = str_replace(' ', '%20', $basePath);
    
    $decodedPath = rawurldecode($path);
    $decodedBase = rawurldecode($basePath);
    if ($basePath !== '' && strpos($decodedPath, $decodedBase) === 0) {
        // Path already starts with basePath
        return $path;
    }
    
    return $basePath . '/' . ltrim($path, '/');
}

// Helper for redirecting
function redirect($url) {
    if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
        // Normalize relative internal paths to absolute URLs using the url() helper.
        if (strpos($url, '/') === 0) {
            $url = url($url);
        } else {
            $url = url('/' . $url);
        }
    }
    header("Location: $url");
    exit;
}

// Router
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// Remove query string for clean path matching
$uri = parse_url($requestUri, PHP_URL_PATH);

// Normalize the subdirectory path (handle spaces vs %20)
$baseDir = dirname($scriptName);
if ($baseDir !== '/') {
    // Decode both to ensure they match (e.g. "WebTech Project" vs "WebTech%20Project")
    $decodedUri = rawurldecode($uri);
    $decodedBase = rawurldecode($baseDir);
    
    if (strpos($decodedUri, $decodedBase) === 0) {
        $uri = substr($decodedUri, strlen($decodedBase));
    }
}

// Ensure $uri starts with / but isn't just empty
if (empty($uri)) $uri = '/';

// Handle routes
$method = $_SERVER['REQUEST_METHOD'];

// Add autoloader for controllers and models
// This keeps the bootstrap simple and avoids manual require statements for every class.
spl_autoload_register(function ($class) {
    if (file_exists("controllers/$class.php")) {
        require_once "controllers/$class.php";
    } elseif (file_exists("models/$class.php")) {
        require_once "models/$class.php";
    }
});

// Check Remember Me cookie if no session user
// This provides a seamless login experience across browser sessions.
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $userModel = new User($pdo);
    $cookieHash = hash('sha256', $_COOKIE['remember_token']);
    // compare hashed token in DB instead of storing raw value
    $user = $userModel->findByRememberToken($cookieHash);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
    }
}

// Simple Router
if ($uri === '/' || $uri === '' || $uri === '/menu') {
    $controller = new ShopController($pdo);
    $controller->menu();
} elseif ($uri === '/login') {
    $controller = new AuthController($pdo);
    if ($method === 'POST') $controller->loginPost();
    else $controller->login();
} elseif ($uri === '/register') {
    $controller = new AuthController($pdo);
    if ($method === 'POST') $controller->registerPost();
    else $controller->register();
} elseif ($uri === '/logout') {
    $controller = new AuthController($pdo);
    $controller->logout();
} elseif ($uri === '/profile') {
    $controller = new UserController($pdo);
    if ($method === 'POST') $controller->updateProfile();
    else $controller->profile();
} elseif ($uri === '/cart') {
    $controller = new ShopController($pdo);
    $controller->cart();
} elseif ($uri === '/checkout') {
    $controller = new ShopController($pdo);
    if ($method === 'POST') $controller->checkoutPost();
    else $controller->checkout();
} elseif ($uri === '/confirmation') {
    $controller = new ShopController($pdo);
    $controller->confirmation();
} elseif ($uri === '/my-orders') {
    $controller = new OrderController($pdo);
    $controller->myOrders();
} elseif ($uri === '/admin') {
    $controller = new AdminController($pdo);
    $controller->dashboard();
} elseif ($uri === '/admin/categories') {
    $controller = new AdminController($pdo);
    if ($method === 'POST') $controller->manageCategoriesPost();
    else $controller->categories();
} elseif ($uri === '/admin/menu-items') {
    $controller = new AdminController($pdo);
    if ($method === 'POST') $controller->manageMenuItemsPost();
    else $controller->menuItems();
} elseif ($uri === '/admin/orders') {
    $controller = new AdminController($pdo);
    $controller->orders();
} elseif (preg_match('#^/api/menu-items/toggle$#', $uri)) {
    $controller = new ApiController($pdo);
    $controller->toggleMenuItem();
} elseif (preg_match('#^/api/cart/add$#', $uri)) {
    $controller = new ApiController($pdo);
    $controller->addToCart();
} elseif (preg_match('#^/api/cart/remove$#', $uri)) {
    $controller = new ApiController($pdo);
    $controller->removeFromCart();
} elseif (preg_match('#^/api/cart/update$#', $uri)) {
    $controller = new ApiController($pdo);
    $controller->updateCartQuantity();
} elseif (preg_match('#^/api/menu-items/search$#', $uri)) {
    $controller = new ApiController($pdo);
    $controller->searchMenuItems();
} elseif (preg_match('#^/api/orders/(\d+)(/status)?$#', $uri, $matches)) {
    $controller = new ApiController($pdo);
    if ($method === 'PUT') $controller->updateOrderStatus($matches[1]);
    else $controller->getOrderStatus($matches[1]);
} else {
    // fallback when no route matches; keep it explicit rather than redirecting blindly
    http_response_code(404);
    echo "404 Not Found";
}
