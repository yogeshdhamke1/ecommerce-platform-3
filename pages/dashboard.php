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
$total_orders = count($user_orders);
$total_spent = array_sum(array_column($user_orders, 'total'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Welcome, <?php echo $_SESSION['full_name']; ?>!</h1>
            <p class="text-gray-600">Manage your account and track your orders</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $total_orders; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Spent</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo formatPrice($total_spent); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-heart text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Wishlist Items</p>
                        <p class="text-2xl font-bold text-gray-900">0</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-star text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Reviews</p>
                        <p class="text-2xl font-bold text-gray-900">0</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Recent Orders</h2>
                    <?php if (empty($user_orders)): ?>
                        <p class="text-gray-500">No orders yet. <a href="../index.php" class="text-blue-600">Start shopping</a></p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach (array_slice($user_orders, 0, 5) as $order): ?>
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-semibold">Order #<?php echo $order['id']; ?></p>
                                            <p class="text-sm text-gray-600"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold"><?php echo formatPrice($order['total']); ?></p>
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4">
                            <a href="orders.php" class="text-blue-600 hover:text-blue-700">View All Orders →</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="profile.php" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-user text-blue-600 mr-3"></i>
                            <span>Edit Profile</span>
                        </a>
                        <a href="orders.php" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-box text-green-600 mr-3"></i>
                            <span>My Orders</span>
                        </a>
                        <a href="wishlist.php" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-heart text-purple-600 mr-3"></i>
                            <span>Wishlist</span>
                        </a>
                        <a href="reviews.php" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-star text-yellow-600 mr-3"></i>
                            <span>My Reviews</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>