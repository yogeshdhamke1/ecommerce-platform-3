<?php
require_once '../config/config.php';
require_once '../classes/Product.php';

header('Content-Type: application/json');

if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    echo json_encode([]);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

$query = trim($_GET['q']);
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

$results = $product->searchProducts($query, $limit);

$suggestions = [];
foreach ($results as $result) {
    $suggestions[] = [
        'id' => $result['id'],
        'name' => $result['name'],
        'category' => $result['category_name'],
        'price' => formatPrice($result['price']),
        'image' => $result['image']
    ];
}

echo json_encode($suggestions);
?>