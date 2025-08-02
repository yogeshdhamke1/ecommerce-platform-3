<?php
require_once '../config/config.php';
require_once '../classes/Coupons.php';
require_once '../classes/Cart.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $coupon_code = $input['coupon_code'] ?? null;
    
    if ($coupon_code) {
        $database = new Database();
        $db = $database->getConnection();
        $coupons = new Coupons($db);
        $cart = new Cart($db);
        
        $total = $cart->getCartTotal($_SESSION['user_id']);
        $coupon = $coupons->validateCoupon($coupon_code, $total);
        
        if ($coupon) {
            $discount = $coupons->applyCoupon($coupon, $total);
            $new_total = $total - $discount;
            
            $_SESSION['applied_coupon'] = $coupon;
            $_SESSION['discount_amount'] = $discount;
            
            echo json_encode([
                'success' => true,
                'discount' => formatPrice($discount),
                'new_total' => formatPrice($new_total),
                'message' => 'Coupon applied successfully!'
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid or expired coupon']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Coupon code required']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>