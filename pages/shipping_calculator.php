<?php
require_once '../config/config.php';
require_once '../classes/Shipping.php';

header('Content-Type: application/json');

if ($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    $shipping = new Shipping($db);
    
    $destination = [
        'pincode' => $_POST['pincode'] ?? '',
        'city' => $_POST['city'] ?? '',
        'state' => $_POST['state'] ?? ''
    ];
    
    $weight = floatval($_POST['weight'] ?? 1);
    $dimensions = [
        'length' => floatval($_POST['length'] ?? 10),
        'width' => floatval($_POST['width'] ?? 10),
        'height' => floatval($_POST['height'] ?? 5)
    ];
    
    if (!$shipping->validatePincode($destination['pincode'])) {
        echo json_encode(['error' => 'Invalid pincode']);
        exit;
    }
    
    $rates = $shipping->calculateShippingRates($destination, $weight, $dimensions);
    
    // Add delivery estimates
    foreach ($rates as &$rate) {
        $rate['estimate'] = $shipping->getDeliveryEstimate($destination['pincode'], $rate['method']);
        $rate['formatted_rate'] = formatPrice($rate['rate']);
    }
    
    echo json_encode(['success' => true, 'rates' => $rates]);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>