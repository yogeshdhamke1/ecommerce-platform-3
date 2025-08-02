<?php
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Check if user is admin
$admin_query = "SELECT is_admin FROM users WHERE id = ?";
$admin_stmt = $db->prepare($admin_query);
$admin_stmt->execute([$_SESSION['user_id']]);
$user_data = $admin_stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_data || !$user_data['is_admin']) {
    header("Location: ../index.php");
    exit();
}

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_method'])) {
        $query = "INSERT INTO shipping_methods (name, description, base_rate, per_kg_rate, delivery_time, carrier, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$_POST['name'], $_POST['description'], $_POST['base_rate'], $_POST['per_kg_rate'], $_POST['delivery_time'], $_POST['carrier'], $_POST['sort_order']]);
        $success = "Shipping method added successfully!";
    }
    
    if (isset($_POST['update_method'])) {
        $query = "UPDATE shipping_methods SET name=?, description=?, base_rate=?, per_kg_rate=?, delivery_time=?, carrier=?, sort_order=?, active=? WHERE id=?";
        $stmt = $db->prepare($query);
        $stmt->execute([$_POST['name'], $_POST['description'], $_POST['base_rate'], $_POST['per_kg_rate'], $_POST['delivery_time'], $_POST['carrier'], $_POST['sort_order'], isset($_POST['active']) ? 1 : 0, $_POST['method_id']]);
        $success = "Shipping method updated successfully!";
    }
    
    if (isset($_POST['delete_method'])) {
        $query = "DELETE FROM shipping_methods WHERE id=?";
        $stmt = $db->prepare($query);
        $stmt->execute([$_POST['method_id']]);
        $success = "Shipping method deleted successfully!";
    }
}

// Get shipping methods
$methods_query = "SELECT * FROM shipping_methods ORDER BY sort_order, name";
$methods_stmt = $db->prepare($methods_query);
$methods_stmt->execute();
$methods = $methods_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get shipping zones
$zones_query = "SELECT * FROM shipping_zones ORDER BY name";
$zones_stmt = $db->prepare($zones_query);
$zones_stmt->execute();
$zones = $zones_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Management - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include 'includes/admin_header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-shipping-fast mr-3 text-blue-600"></i>Shipping Management
            </h1>
            <button onclick="openAddModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Add Shipping Method
            </button>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <!-- Shipping Methods Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Shipping Methods</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carrier</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Base Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Per KG</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delivery Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($methods as $method): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($method['name']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($method['description']); ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($method['carrier']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900">₹<?php echo number_format($method['base_rate'], 2); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900">₹<?php echo number_format($method['per_kg_rate'], 2); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($method['delivery_time']); ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $method['active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $method['active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium space-x-2">
                                    <button onclick="editMethod(<?php echo htmlspecialchars(json_encode($method)); ?>)" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Delete this shipping method?')">
                                        <input type="hidden" name="method_id" value="<?php echo $method['id']; ?>">
                                        <button type="submit" name="delete_method" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Shipping Zones -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Shipping Zones</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zone Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pincode Pattern</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Base Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($zones as $zone): ?>
                            <tr>
                                <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($zone['name']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500"><?php echo htmlspecialchars($zone['description']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($zone['pincode_pattern']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900">₹<?php echo number_format($zone['base_rate'], 2); ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $zone['active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $zone['active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Modal -->
    <div id="methodModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold">Add Shipping Method</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="methodForm" method="POST" class="space-y-4">
                <input type="hidden" id="methodId" name="method_id">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Method Name</label>
                    <input type="text" id="methodName" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="methodDescription" name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Base Rate (₹)</label>
                        <input type="number" id="baseRate" name="base_rate" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Per KG Rate (₹)</label>
                        <input type="number" id="perKgRate" name="per_kg_rate" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Time</label>
                    <input type="text" id="deliveryTime" name="delivery_time" placeholder="e.g., 2-3 days" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Carrier</label>
                    <input type="text" id="carrier" name="carrier" placeholder="e.g., BlueDart" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" id="sortOrder" name="sort_order" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div id="activeCheckbox" class="hidden">
                    <label class="flex items-center">
                        <input type="checkbox" id="active" name="active" class="mr-2">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
                    <button type="submit" id="submitBtn" name="add_method" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Add Method</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Shipping Method';
            document.getElementById('methodForm').reset();
            document.getElementById('methodId').value = '';
            document.getElementById('submitBtn').name = 'add_method';
            document.getElementById('submitBtn').textContent = 'Add Method';
            document.getElementById('activeCheckbox').classList.add('hidden');
            document.getElementById('methodModal').classList.remove('hidden');
        }
        
        function editMethod(method) {
            document.getElementById('modalTitle').textContent = 'Edit Shipping Method';
            document.getElementById('methodId').value = method.id;
            document.getElementById('methodName').value = method.name;
            document.getElementById('methodDescription').value = method.description;
            document.getElementById('baseRate').value = method.base_rate;
            document.getElementById('perKgRate').value = method.per_kg_rate;
            document.getElementById('deliveryTime').value = method.delivery_time;
            document.getElementById('carrier').value = method.carrier;
            document.getElementById('sortOrder').value = method.sort_order;
            document.getElementById('active').checked = method.active == 1;
            document.getElementById('submitBtn').name = 'update_method';
            document.getElementById('submitBtn').textContent = 'Update Method';
            document.getElementById('activeCheckbox').classList.remove('hidden');
            document.getElementById('methodModal').classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('methodModal').classList.add('hidden');
        }
    </script>
</body>
</html>