<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $currency = $input['currency'] ?? null;
    
    $valid_currencies = ['USD', 'EUR', 'GBP', 'INR', 'JPY'];
    
    if ($currency && in_array($currency, $valid_currencies)) {
        $_SESSION['currency'] = $currency;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid currency']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>