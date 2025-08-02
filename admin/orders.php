<?php
require_once '../config/config.php';
require_once '../classes/Order.php';

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

$order = new Order($db);

// Handle status update
if ($_POST && isset($_POST['update_status'])) {
    $order->updateOrderStatus($_POST['order_id'], $_POST['status']);
    $success = "Order status updated successfully!";
}

// Get all orders
$query = "SELECT o.*, u.full_name, u.email FROM orders o 
          JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include 'includes/admin_header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Orders Management</h1>
            <p class="text-gray-600">Manage customer orders and update status</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($orders as $ord): ?>
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900">#<?php echo $ord['id']; ?></td>
                            <td class="px-6 py-4">
                                <div>
                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($ord['full_name']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($ord['email']); ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-blue-600"><?php echo formatPrice($ord['total']); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    <?php echo $ord['status'] == 'completed' ? 'bg-green-100 text-green-800' : 
                                        ($ord['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                                    <?php echo ucfirst($ord['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo date('M d, Y', strtotime($ord['created_at'])); ?></td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <button onclick="viewOrder(<?php echo $ord['id']; ?>)" 
                                        class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="updateStatus(<?php echo $ord['id']; ?>, '<?php echo $ord['status']; ?>')" 
                                        class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Update Order Status</h2>
                <form method="POST">
                    <input type="hidden" name="update_status" value="1">
                    <input type="hidden" name="order_id" id="statusOrderId">
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="orderStatus" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeStatusModal()" 
                                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function viewOrder(orderId) {
            window.open(`../pages/order_details.php?id=${orderId}`, '_blank');
        }

        function updateStatus(orderId, currentStatus) {
            document.getElementById('statusOrderId').value = orderId;
            document.getElementById('orderStatus').value = currentStatus;
            document.getElementById('statusModal').classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }
    </script>
</body>
</html>