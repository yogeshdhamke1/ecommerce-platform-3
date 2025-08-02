<?php
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Check admin status
$admin_query = "SELECT is_admin FROM users WHERE id = ?";
$admin_stmt = $db->prepare($admin_query);
$admin_stmt->execute([$_SESSION['user_id']]);
$user = $admin_stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !$user['is_admin']) {
    header("Location: ../index.php");
    exit();
}

// Sales Report
$sales_query = "SELECT 
    DATE(created_at) as date,
    COUNT(*) as orders,
    SUM(total) as revenue
    FROM orders 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date DESC";
$sales_stmt = $db->prepare($sales_query);
$sales_stmt->execute();
$sales_data = $sales_stmt->fetchAll(PDO::FETCH_ASSOC);

// Top Products
$products_query = "SELECT 
    p.name,
    SUM(oi.quantity) as total_sold,
    SUM(oi.quantity * oi.price) as revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY p.id
    ORDER BY total_sold DESC
    LIMIT 10";
$products_stmt = $db->prepare($products_query);
$products_stmt->execute();
$top_products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);

// Monthly Stats
$monthly_query = "SELECT 
    MONTH(created_at) as month,
    YEAR(created_at) as year,
    COUNT(*) as orders,
    SUM(total) as revenue
    FROM orders 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY year DESC, month DESC";
$monthly_stmt = $db->prepare($monthly_query);
$monthly_stmt->execute();
$monthly_data = $monthly_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include 'includes/admin_header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Reports & Analytics</h1>
            <p class="text-gray-600">Business insights and performance metrics</p>
        </div>

        <!-- Daily Sales Report -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">
                <i class="fas fa-chart-line text-blue-600 mr-2"></i>Daily Sales (Last 30 Days)
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($sales_data as $sale): ?>
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900"><?php echo date('M d, Y', strtotime($sale['date'])); ?></td>
                                <td class="px-4 py-2 text-sm text-gray-900"><?php echo $sale['orders']; ?></td>
                                <td class="px-4 py-2 text-sm font-medium text-blue-600"><?php echo formatPrice($sale['revenue']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Products -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">
                    <i class="fas fa-trophy text-yellow-600 mr-2"></i>Top Selling Products
                </h2>
                <div class="space-y-4">
                    <?php foreach ($top_products as $index => $product): ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">
                                    <?php echo $index + 1; ?>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $product['total_sold']; ?> sold</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium text-blue-600"><?php echo formatPrice($product['revenue']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Monthly Performance -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">
                    <i class="fas fa-calendar-alt text-green-600 mr-2"></i>Monthly Performance
                </h2>
                <div class="space-y-4">
                    <?php foreach ($monthly_data as $month): ?>
                        <div class="flex items-center justify-between border-b pb-3">
                            <div>
                                <div class="font-medium text-gray-900">
                                    <?php echo date('F Y', mktime(0, 0, 0, $month['month'], 1, $month['year'])); ?>
                                </div>
                                <div class="text-sm text-gray-500"><?php echo $month['orders']; ?> orders</div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium text-green-600"><?php echo formatPrice($month['revenue']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">
                <i class="fas fa-download text-purple-600 mr-2"></i>Export Reports
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition">
                    <i class="fas fa-file-csv mr-2"></i>Export Sales CSV
                </button>
                <button class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition">
                    <i class="fas fa-file-excel mr-2"></i>Export Products Excel
                </button>
                <button class="bg-red-600 text-white px-6 py-3 rounded-md hover:bg-red-700 transition">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF Report
                </button>
            </div>
        </div>
    </div>
</body>
</html>