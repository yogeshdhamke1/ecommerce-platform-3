<?php
require_once '../config/config.php';
require_once '../classes/Cart.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $product_id = $input['product_id'] ?? null;
    $quantity = $input['quantity'] ?? 1;
    
    if ($product_id) {
        $database = new Database();
        $db = $database->getConnection();
        $cart = new Cart($db);
        
        if ($cart->addToCart($_SESSION['user_id'], $product_id, $quantity)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to add to cart']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>