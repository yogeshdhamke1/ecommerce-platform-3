<?php
require_once '../config/config.php';
require_once '../classes/Order.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$order = new Order($db);

$user_orders = $order->getUserOrders($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
            <p class="text-gray-600">Track and manage your orders</p>
        </div>
        
        <?php if (empty($user_orders)): ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-6"></i>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">No Orders Yet</h2>
                <p class="text-gray-500 mb-8">You haven't placed any orders yet.</p>
                <a href="../index.php" class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 transition">
                    <i class="fas fa-shopping-bag mr-2"></i>Start Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($user_orders as $order): ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold">Order #<?php echo $order['id']; ?></h3>
                                <p class="text-gray-600">Placed on <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                            </div>
                            <div class="mt-4 md:mt-0 text-right">
                                <p class="text-2xl font-bold text-blue-600"><?php echo formatPrice($order['total']); ?></p>
                                <span class="inline-block px-3 py-1 text-sm rounded-full 
                                    <?php echo $order['status'] == 'completed' ? 'bg-green-100 text-green-800' : 
                                        ($order['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="border-t pt-4">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="text-sm text-gray-600">
                                    <p><strong>Shipping Address:</strong></p>
                                    <p><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                                </div>
                                <div class="mt-4 md:mt-0 flex space-x-3">
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" 
                                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                                        <i class="fas fa-eye mr-1"></i>View Details
                                    </a>
                                    <?php if ($order['status'] == 'completed'): ?>
                                        <a href="invoice.php?order_id=<?php echo $order['id']; ?>" 
                                           class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                                            <i class="fas fa-download mr-1"></i>Invoice
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>