<?php
require_once '../config/config.php';
require_once '../classes/Coupons.php';

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

$coupons = new Coupons($db);

// Handle coupon operations
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $query = "INSERT INTO coupons (code, type, value, minimum_amount, max_discount, usage_limit, expires_at, is_active) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
                $stmt = $db->prepare($query);
                $expires = $_POST['expires_at'] ? $_POST['expires_at'] : null;
                $stmt->execute([
                    $_POST['code'], $_POST['type'], $_POST['value'], 
                    $_POST['minimum_amount'], $_POST['max_discount'], 
                    $_POST['usage_limit'], $expires
                ]);
                $success = "Coupon created successfully!";
                break;
            case 'toggle':
                $query = "UPDATE coupons SET is_active = NOT is_active WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$_POST['id']]);
                $success = "Coupon status updated!";
                break;
        }
    }
}

$all_coupons = $coupons->getActiveCoupons();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupons Management - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include 'includes/admin_header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Coupons & Discounts</h1>
            <button onclick="openAddModal()" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Add Coupon
            </button>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($all_coupons as $coupon): ?>
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900"><?php echo $coupon['code']; ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo ucfirst($coupon['type']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo $coupon['type'] == 'percentage' ? $coupon['value'] . '%' : formatPrice($coupon['value']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo $coupon['usage_count']; ?>/<?php echo $coupon['usage_limit'] ?: '∞'; ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $coupon['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $coupon['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="id" value="<?php echo $coupon['id']; ?>">
                                    <button type="submit" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-toggle-<?php echo $coupon['is_active'] ? 'on' : 'off'; ?>"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Coupon Modal -->
    <div id="couponModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Add New Coupon</h2>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="add">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Coupon Code</label>
                        <input type="text" name="code" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                        <input type="number" name="value" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Amount</label>
                        <input type="number" name="minimum_amount" step="0.01" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Discount (for %)</label>
                        <input type="number" name="max_discount" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usage Limit</label>
                        <input type="number" name="usage_limit" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expires At</label>
                        <input type="datetime-local" name="expires_at" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Create Coupon
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('couponModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('couponModal').classList.add('hidden');
        }
    </script>
</body>
</html>