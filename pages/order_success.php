<?php
require_once '../config/config.php';
require_once '../classes/Order.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$order = new Order($db);

$order_details = $order->getOrderById($_GET['order_id']);

if (!$order_details || $order_details['user_id'] != $_SESSION['user_id']) {
    header("Location: orders.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-2xl mx-auto text-center">
            <!-- Success Icon -->
            <div class="mb-8">
                <div class="bg-green-100 rounded-full p-6 inline-block">
                    <i class="fas fa-check-circle text-green-600 text-6xl"></i>
                </div>
            </div>
            
            <!-- Success Message -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Order Placed Successfully!</h1>
            <p class="text-lg text-gray-600 mb-8">
                Thank you for your purchase. Your order has been received and is being processed.
            </p>
            
            <!-- Order Details Card -->
            <div class="bg-white rounded-lg shadow-md p-8 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                    <div>
                        <p class="text-sm text-gray-600">Order Number</p>
                        <p class="text-lg font-semibold text-blue-600">#<?php echo $order_details['id']; ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Order Date</p>
                        <p class="text-lg font-semibold text-gray-900"><?php echo date('M d, Y', strtotime($order_details['created_at'])); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Amount</p>
                        <p class="text-lg font-semibold text-green-600"><?php echo formatPrice($order_details['total']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Payment Status</p>
                        <p class="text-lg font-semibold text-yellow-600">Processing</p>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Shipping Address</p>
                    <p class="text-gray-900"><?php echo nl2br(htmlspecialchars($order_details['shipping_address'])); ?></p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="space-y-4 md:space-y-0 md:space-x-4 md:flex md:justify-center">
                <a href="order_details.php?id=<?php echo $order_details['id']; ?>" 
                   class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 transition inline-block">
                    <i class="fas fa-eye mr-2"></i>View Order Details
                </a>
                <a href="invoice.php?order_id=<?php echo $order_details['id']; ?>" 
                   class="bg-green-600 text-white px-8 py-3 rounded-md hover:bg-green-700 transition inline-block">
                    <i class="fas fa-file-invoice mr-2"></i>View Invoice
                </a>
                <a href="../index.php" 
                   class="bg-gray-600 text-white px-8 py-3 rounded-md hover:bg-gray-700 transition inline-block">
                    <i class="fas fa-shopping-bag mr-2"></i>Continue Shopping
                </a>
            </div>
            
            <!-- Additional Information -->
            <div class="mt-12 bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">What's Next?</h3>
                <div class="text-left space-y-3 text-blue-800">
                    <div class="flex items-start">
                        <i class="fas fa-envelope text-blue-600 mr-3 mt-1"></i>
                        <p>You'll receive an order confirmation email shortly with all the details.</p>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-truck text-blue-600 mr-3 mt-1"></i>
                        <p>We'll notify you when your order ships with tracking information.</p>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-headset text-blue-600 mr-3 mt-1"></i>
                        <p>Need help? Contact our customer support at info@ecommerce.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>