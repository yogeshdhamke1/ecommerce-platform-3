<?php
require_once '../config/config.php';

// Check if user is admin
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

// Get statistics
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM products) as total_products,
    (SELECT COUNT(*) FROM orders) as total_orders,
    (SELECT SUM(total) FROM orders WHERE status = 'completed') as total_revenue";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Get recent orders
$orders_query = "SELECT o.*, u.full_name FROM orders o 
                 JOIN users u ON o.user_id = u.id 
                 ORDER BY o.created_at DESC LIMIT 10";
$orders_stmt = $db->prepare($orders_query);
$orders_stmt->execute();
$recent_orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <?php include 'includes/admin_header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="flex">
            <div class="w-64 bg-white shadow-md">
                <div class="p-4">
                    <nav class="space-y-2">
                        <a href="index.php" class="flex items-center px-4 py-2 text-gray-700 bg-blue-100 rounded-md">
                            <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                        </a>
                        <a href="products.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-box mr-3"></i>Products
                        </a>
                        <a href="categories.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-tags mr-3"></i>Categories
                        </a>
                        <a href="orders.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-shopping-cart mr-3"></i>Orders
                        </a>
                        <a href="users.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-users mr-3"></i>Users
                        </a>
                        <a href="reports.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-chart-bar mr-3"></i>Reports
                        </a>
                    </nav>
                </div>
            </div>
            
            <div class="flex-1 p-8">
                <h2>Dashboard</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Total Users</p>
                                <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_users']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-box text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Total Products</p>
                                <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_products']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <i class="fas fa-shopping-cart text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Total Orders</p>
                                <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_orders']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="fas fa-rupee-sign text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Total Revenue</p>
                                <p class="text-2xl font-bold text-gray-900"><?php echo formatPrice($stats['total_revenue'] ?: 0); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Real-Time Analytics Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold mb-4">Sales Overview (Last 7 Days)</h3>
                        <canvas id="salesChart" width="400" height="200"></canvas>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold mb-4">Order Status Distribution</h3>
                        <canvas id="statusChart" width="400" height="200"></canvas>
                    </div>
                </div>
                
                <!-- Live Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h4 class="text-md font-semibold mb-3">Today's Stats</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Orders:</span>
                                <span id="todayOrders" class="font-semibold">Loading...</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Revenue:</span>
                                <span id="todayRevenue" class="font-semibold text-green-600">Loading...</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">New Users:</span>
                                <span id="todayUsers" class="font-semibold text-blue-600">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h4 class="text-md font-semibold mb-3">Top Products</h4>
                        <div id="topProducts" class="space-y-2">
                            Loading...
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h4 class="text-md font-semibold mb-3">Recent Activity</h4>
                        <div id="recentActivity" class="space-y-2 text-sm">
                            Loading...
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Recent Orders</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td class="px-6 py-4 font-medium text-gray-900">#<?php echo $order['id']; ?></td>
                                        <td class="px-6 py-4 text-gray-900"><?php echo $order['full_name']; ?></td>
                                        <td class="px-6 py-4 font-medium text-blue-600"><?php echo formatPrice($order['total']); ?></td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs rounded-full <?php echo $order['status'] == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-900"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Initialize Charts
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        
        // Sales Chart
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['6 days ago', '5 days ago', '4 days ago', '3 days ago', '2 days ago', 'Yesterday', 'Today'],
                datasets: [{
                    label: 'Sales (₹)',
                    data: [12000, 19000, 8000, 15000, 22000, 18000, 25000],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
        
        // Status Chart
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending', 'Processing', 'Cancelled'],
                datasets: [{
                    data: [65, 20, 10, 5],
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
        
        // Real-time data updates
        function updateDashboard() {
            fetch('analytics_api.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('todayOrders').textContent = data.todayOrders || '0';
                    document.getElementById('todayRevenue').textContent = '₹' + (data.todayRevenue || '0');
                    document.getElementById('todayUsers').textContent = data.todayUsers || '0';
                    
                    // Update top products
                    const topProducts = document.getElementById('topProducts');
                    topProducts.innerHTML = data.topProducts.map(product => 
                        `<div class="flex justify-between text-sm">
                            <span>${product.name}</span>
                            <span class="font-semibold">${product.sales}</span>
                        </div>`
                    ).join('');
                    
                    // Update recent activity
                    const recentActivity = document.getElementById('recentActivity');
                    recentActivity.innerHTML = data.recentActivity.map(activity => 
                        `<div class="text-gray-600">
                            <i class="fas fa-${activity.icon} mr-2"></i>${activity.text}
                        </div>`
                    ).join('');
                })
                .catch(error => console.error('Error:', error));
        }
        
        // Update dashboard every 30 seconds
        updateDashboard();
        setInterval(updateDashboard, 30000);
    </script>
</body>
</html>