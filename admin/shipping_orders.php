<?php
require_once '../config/config.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../pages/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get orders with shipping information
$orders_query = "SELECT o.*, u.full_name, u.email, 
                 COUNT(oi.id) as item_count,
                 GROUP_CONCAT(p.name SEPARATOR ', ') as products
                 FROM orders o 
                 JOIN users u ON o.user_id = u.id 
                 LEFT JOIN order_items oi ON o.id = oi.order_id
                 LEFT JOIN products p ON oi.product_id = p.id
                 GROUP BY o.id 
                 ORDER BY o.created_at DESC";
$orders_stmt = $db->prepare($orders_query);
$orders_stmt->execute();
$orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Orders - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include 'includes/admin_header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">
            <i class="fas fa-truck mr-3 text-blue-600"></i>Shipping Orders
        </h1>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shipping Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shipping Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-blue-600">#<?php echo $order['id']; ?></td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($order['full_name']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($order['email']); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars(substr($order['products'], 0, 50)) . '...'; ?></div>
                                    <div class="text-xs text-gray-500"><?php echo $order['item_count']; ?> items</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><?php echo $order['shipping_method'] ?: 'Not specified'; ?></div>
                                    <div class="text-xs text-gray-500"><?php echo $order['delivery_estimate'] ?: ''; ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <?php echo $order['shipping_cost'] ? formatPrice($order['shipping_cost']) : 'Free'; ?>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900"><?php echo formatPrice($order['total']); ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        <?php 
                                        switch($order['status']) {
                                            case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'processing': echo 'bg-blue-100 text-blue-800'; break;
                                            case 'shipped': echo 'bg-purple-100 text-purple-800'; break;
                                            case 'delivered': echo 'bg-green-100 text-green-800'; break;
                                            case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                            default: echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>