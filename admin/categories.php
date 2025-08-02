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

// Handle category operations
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $query = "INSERT INTO categories (name, description) VALUES (?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$_POST['name'], $_POST['description']]);
                $success = "Category added successfully!";
                break;
            case 'update':
                $query = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$_POST['name'], $_POST['description'], $_POST['id']]);
                $success = "Category updated successfully!";
                break;
            case 'delete':
                $query = "DELETE FROM categories WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$_POST['id']]);
                $success = "Category deleted successfully!";
                break;
        }
    }
}

// Get categories with product count
$query = "SELECT c.*, COUNT(p.id) as product_count FROM categories c 
          LEFT JOIN products p ON c.id = p.category_id 
          GROUP BY c.id ORDER BY c.name";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories Management - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include 'includes/admin_header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Categories Management</h1>
            <button onclick="openAddModal()" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Add Category
            </button>
        </div>

        <?php if (isset($success)): ?>
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($categories as $category): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($category['name']); ?></h3>
                        <div class="flex space-x-2">
                            <button onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)" 
                                    class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteCategory(<?php echo $category['id']; ?>)" 
                                    class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($category['description']); ?></p>
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-box mr-2"></i>
                        <?php echo $category['product_count']; ?> Products
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add/Edit Category Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h2 id="modalTitle" class="text-xl font-bold mb-4">Add Category</h2>
                <form id="categoryForm" method="POST">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="categoryId">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                        <input type="text" name="name" id="categoryName" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="categoryDescription" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" 
                                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Category';
            document.getElementById('formAction').value = 'add';
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryModal').classList.remove('hidden');
        }

        function editCategory(category) {
            document.getElementById('modalTitle').textContent = 'Edit Category';
            document.getElementById('formAction').value = 'update';
            document.getElementById('categoryId').value = category.id;
            document.getElementById('categoryName').value = category.name;
            document.getElementById('categoryDescription').value = category.description;
            document.getElementById('categoryModal').classList.remove('hidden');
        }

        function deleteCategory(id) {
            if (confirm('Are you sure you want to delete this category?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function closeModal() {
            document.getElementById('categoryModal').classList.add('hidden');
        }
    </script>
</body>
</html>