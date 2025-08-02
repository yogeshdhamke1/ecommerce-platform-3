<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$product_id = $input['product_id'] ?? 0;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Check if already in wishlist
$check_query = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?";
$stmt = $db->prepare($check_query);
$stmt->execute([$_SESSION['user_id'], $product_id]);

if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Already in wishlist']);
    exit();
}

// Add to wishlist
$query = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
$stmt = $db->prepare($query);

if ($stmt->execute([$_SESSION['user_id'], $product_id])) {
    echo json_encode(['success' => true, 'message' => 'Added to wishlist']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add to wishlist']);
}
?>