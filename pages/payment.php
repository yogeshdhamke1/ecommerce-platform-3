<?php
require_once '../config/config.php';
require_once '../classes/Cart.php';
require_once '../classes/Order.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);
$order = new Order($db);

$cart_items = $cart->getCartItems($_SESSION['user_id']);
$total = $cart->getCartTotal($_SESSION['user_id']);

if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

// Handle payment processing
if ($_POST && isset($_POST['payment_method'])) {
    $shipping_address = $_POST['shipping_address'];
    $payment_method = $_POST['payment_method'];
    
    // Simulate payment processing
    $payment_success = true; // In real implementation, integrate with payment gateway
    
    if ($payment_success) {
        $order_id = $order->createOrder($_SESSION['user_id'], $total, $shipping_address, $_SESSION['currency']);
        if ($order_id) {
            // Store payment info
            $payment_query = "INSERT INTO payments (order_id, payment_method, amount, status) VALUES (?, ?, ?, 'completed')";
            $payment_stmt = $db->prepare($payment_query);
            $payment_stmt->execute([$order_id, $payment_method, $total]);
            
            header("Location: order_success.php?order_id=" . $order_id);
            exit();
        }
    }
    $error = "Payment processing failed. Please try again.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Secure Payment</h1>
        
        <?php if (isset($error)): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-6">Payment Methods</h2>
                    
                    <form method="POST" class="space-y-6">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Shipping Address</label>
                            <textarea name="shipping_address" rows="3" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <label class="flex items-center">
                                    <input type="radio" name="payment_method" value="razorpay" class="mr-3" required>
                                    <div class="flex items-center">
                                        <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                                        <span class="font-medium">Razorpay (UPI, Cards, Net Banking)</span>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="border border-gray-200 rounded-lg p-4">
                                <label class="flex items-center">
                                    <input type="radio" name="payment_method" value="paytm" class="mr-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-mobile-alt text-blue-600 mr-2"></i>
                                        <span class="font-medium">Paytm Wallet</span>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="border border-gray-200 rounded-lg p-4">
                                <label class="flex items-center">
                                    <input type="radio" name="payment_method" value="cod" class="mr-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                                        <span class="font-medium">Cash on Delivery</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 transition font-medium">
                            <i class="fas fa-lock mr-2"></i>Complete Payment - <?php echo formatPrice($total); ?>
                        </button>
                    </form>
                </div>
            </div>
            
            <div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                    <div class="space-y-3">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="flex justify-between text-sm">
                                <span><?php echo htmlspecialchars($item['name']); ?> (<?php echo $item['quantity']; ?>)</span>
                                <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span class="text-blue-600"><?php echo formatPrice($total); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>