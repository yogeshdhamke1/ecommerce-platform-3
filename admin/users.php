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

// Get users with order statistics
$query = "SELECT u.*, 
          COUNT(o.id) as total_orders,
          COALESCE(SUM(o.total), 0) as total_spent,
          MAX(o.created_at) as last_order
          FROM users u 
          LEFT JOIN orders o ON u.id = o.user_id 
          GROUP BY u.id 
          ORDER BY u.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include 'includes/admin_header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
            <p class="text-gray-600">Manage customer accounts and view statistics</p>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Spent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($users as $usr): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 rounded-full p-2 mr-4">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900"><?php echo htmlspecialchars($usr['full_name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($usr['email']); ?></div>
                                        <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($usr['username']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    <?php echo $usr['is_admin'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                                    <?php echo $usr['is_admin'] ? 'Admin' : 'Customer'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo $usr['total_orders']; ?></td>
                            <td class="px-6 py-4 text-sm font-medium text-blue-600"><?php echo formatPrice($usr['total_spent']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo $usr['last_order'] ? date('M d, Y', strtotime($usr['last_order'])) : 'Never'; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo date('M d, Y', strtotime($usr['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- User Statistics -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($users); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Active Customers</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php echo count(array_filter($users, function($u) { return $u['total_orders'] > 0; })); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-crown text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Admins</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php echo count(array_filter($users, function($u) { return $u['is_admin']; })); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-user-plus text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">New This Month</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php 
                            $thisMonth = count(array_filter($users, function($u) { 
                                return date('Y-m', strtotime($u['created_at'])) == date('Y-m'); 
                            }));
                            echo $thisMonth;
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>