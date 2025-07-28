<?php
require_once '../config/config.php';
require_once '../classes/Order.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$order = new Order($db);

$order_details = $order->getOrderById($_GET['id']);
$order_items = $order->getOrderItems($_GET['id']);

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
    <title>Order Details - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <nav class="mb-4">
                <a href="orders.php" class="text-blue-600 hover:text-blue-700">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Orders
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">Order #<?php echo $order_details['id']; ?></h1>
            <p class="text-gray-600">Placed on <?php echo date('M d, Y', strtotime($order_details['created_at'])); ?></p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Order Items</h2>
                    <div class="space-y-4">
                        <?php foreach ($order_items as $item): ?>
                            <div class="flex items-center space-x-4 border-b pb-4">
                                <img src="../assets/images/<?php echo $item['image']; ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     class="w-16 h-16 object-cover rounded-md"
                                     onerror="this.src='../assets/images/demo-product.jpg'">
                                <div class="flex-1">
                                    <h3 class="font-semibold"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="text-gray-600">Quantity: <?php echo $item['quantity']; ?></p>
                                    <p class="text-blue-600 font-semibold"><?php echo formatPrice($item['price']); ?> each</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold"><?php echo formatPrice($item['price'] * $item['quantity']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Shipping Information</h2>
                    <div class="text-gray-700">
                        <p class="mb-2"><strong>Address:</strong></p>
                        <p><?php echo nl2br(htmlspecialchars($order_details['shipping_address'])); ?></p>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>Status:</span>
                            <span class="px-2 py-1 text-sm rounded-full 
                                <?php echo $order_details['status'] == 'completed' ? 'bg-green-100 text-green-800' : 
                                    ($order_details['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                                <?php echo ucfirst($order_details['status']); ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span>Currency:</span>
                            <span><?php echo $order_details['currency']; ?></span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span class="text-blue-600"><?php echo formatPrice($order_details['total']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($order_details['status'] == 'completed'): ?>
                    <a href="invoice.php?order_id=<?php echo $order_details['id']; ?>" 
                       class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 transition text-center block">
                        <i class="fas fa-download mr-2"></i>Download Invoice
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>