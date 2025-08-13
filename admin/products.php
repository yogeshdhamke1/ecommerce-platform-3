<?php
require_once '../config/config.php';
require_once '../classes/Product.php';

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

$product = new Product($db);

// Handle product operations
if ($_POST) {
    if (isset($_POST['action'])) {
        // Handle image upload
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
            $upload_dir = '../assets/images/';
            $file_name = time() . '_' . $_FILES['image_file']['name'];
            $upload_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_path)) {
                $_POST['image'] = $file_name;
            }
        } elseif (isset($_POST['image_url']) && !empty($_POST['image_url'])) {
            $_POST['image'] = $_POST['image_url'];
        } elseif ($_POST['action'] == 'update' && empty($_POST['image'])) {
            // Keep existing image if no new image provided
            unset($_POST['image']);
        } else {
            $_POST['image'] = 'demo-product.jpg';
        }
        
        switch ($_POST['action']) {
            case 'add':
                $product->addProduct($_POST);
                $success = "Product added successfully!";
                break;
            case 'update':
                $product->updateProduct($_POST['id'], $_POST);
                $success = "Product updated successfully!";
                break;
            case 'delete':
                $product->deleteProduct($_POST['id']);
                $success = "Product deleted successfully!";
                break;
        }
    }
}

$products = $product->getAllProducts();

// Get categories
$cat_query = "SELECT * FROM categories ORDER BY name";
$cat_stmt = $db->prepare($cat_query);
$cat_stmt->execute();
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include 'includes/admin_header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Products Management</h1>
            <button onclick="openAddModal()" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Add Product
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" id="searchInput" placeholder="Search products..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="categoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['name']; ?>"><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
                    <select id="stockFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Stock</option>
                        <option value="in-stock">In Stock</option>
                        <option value="low-stock">Low Stock (≤5)</option>
                        <option value="out-of-stock">Out of Stock</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="clearFilters()" class="w-full bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">
                        <i class="fas fa-times mr-2"></i>Clear
                    </button>
                </div>
            </div>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="productsTable">
                    <?php foreach ($products as $prod): ?>
                        <tr class="product-row" data-name="<?php echo strtolower($prod['name']); ?>" data-category="<?php echo $prod['category_name']; ?>" data-stock="<?php echo $prod['stock']; ?>">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img src="../assets/images/<?php echo $prod['image']; ?>" 
                                         alt="<?php echo htmlspecialchars($prod['name']); ?>"
                                         class="w-12 h-12 object-cover rounded-md mr-4"
                                         onerror="this.src='../assets/images/demo-product.jpg'">
                                    <div>
                                        <div class="font-medium text-gray-900"><?php echo htmlspecialchars($prod['name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo substr($prod['description'], 0, 50); ?>...</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo $prod['category_name']; ?></td>
                            <td class="px-6 py-4 text-sm font-medium text-blue-600"><?php echo formatPrice($prod['price']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo $prod['stock']; ?></td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <button onclick="editProduct(<?php echo htmlspecialchars(json_encode($prod)); ?>)" 
                                        class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteProduct(<?php echo $prod['id']; ?>)" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h2 id="modalTitle" class="text-xl font-bold mb-4">Add Product</h2>
                <form id="productForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="productId">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                        <input type="text" name="name" id="productName" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="productDescription" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price (INR)</label>
                        <input type="number" name="price" id="productPrice" step="0.01" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category_id" id="productCategory" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                        <input type="number" name="stock" id="productStock" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="image_type" value="upload" checked class="mr-2" onchange="toggleImageInput()">
                                    Upload Image
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="image_type" value="url" class="mr-2" onchange="toggleImageInput()">
                                    Image URL
                                </label>
                            </div>
                            <div id="uploadOption">
                                <input type="file" name="image_file" id="imageFile" accept="image/*" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div id="urlOption" style="display: none;">
                                <input type="text" name="image_url" id="imageUrl" placeholder="https://example.com/image.jpg" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <input type="hidden" name="image" id="productImage">
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" 
                                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Save Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Product';
            document.getElementById('formAction').value = 'add';
            document.getElementById('productForm').reset();
            document.getElementById('productModal').classList.remove('hidden');
        }

        function editProduct(product) {
            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('formAction').value = 'update';
            document.getElementById('productId').value = product.id;
            document.getElementById('productName').value = product.name;
            document.getElementById('productDescription').value = product.description;
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productCategory').value = product.category_id;
            document.getElementById('productStock').value = product.stock;
            
            // Handle image display for edit
            if (product.image && product.image !== 'demo-product.jpg') {
                document.querySelector('input[name="image_type"][value="url"]').checked = true;
                document.getElementById('imageUrl').value = product.image;
                toggleImageInput();
            } else {
                document.querySelector('input[name="image_type"][value="upload"]').checked = true;
                toggleImageInput();
            }
            
            document.getElementById('productModal').classList.remove('hidden');
        }

        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
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
            document.getElementById('productModal').classList.add('hidden');
        }

        // Filter functionality
        function filterProducts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const stockFilter = document.getElementById('stockFilter').value;
            const rows = document.querySelectorAll('.product-row');

            rows.forEach(row => {
                const name = row.dataset.name;
                const category = row.dataset.category;
                const stock = parseInt(row.dataset.stock);
                
                let showRow = true;

                // Search filter
                if (searchTerm && !name.includes(searchTerm)) {
                    showRow = false;
                }

                // Category filter
                if (categoryFilter && category !== categoryFilter) {
                    showRow = false;
                }

                // Stock filter
                if (stockFilter) {
                    if (stockFilter === 'in-stock' && stock <= 0) showRow = false;
                    if (stockFilter === 'low-stock' && stock > 5) showRow = false;
                    if (stockFilter === 'out-of-stock' && stock > 0) showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            });
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('categoryFilter').value = '';
            document.getElementById('stockFilter').value = '';
            filterProducts();
        }

        // Add event listeners
        document.getElementById('searchInput').addEventListener('input', filterProducts);
        document.getElementById('categoryFilter').addEventListener('change', filterProducts);
        document.getElementById('stockFilter').addEventListener('change', filterProducts);

        // Image input toggle
        function toggleImageInput() {
            const uploadRadio = document.querySelector('input[name="image_type"][value="upload"]');
            const uploadOption = document.getElementById('uploadOption');
            const urlOption = document.getElementById('urlOption');
            
            if (uploadRadio.checked) {
                uploadOption.style.display = 'block';
                urlOption.style.display = 'none';
            } else {
                uploadOption.style.display = 'none';
                urlOption.style.display = 'block';
            }
        }

        // Handle form submission
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const uploadRadio = document.querySelector('input[name="image_type"][value="upload"]');
            const imageFile = document.getElementById('imageFile');
            const imageUrl = document.getElementById('imageUrl');
            const productImage = document.getElementById('productImage');
            const formAction = document.getElementById('formAction').value;
            
            if (uploadRadio.checked) {
                if (imageFile.files.length > 0) {
                    productImage.value = imageFile.files[0].name;
                } else if (formAction === 'update') {
                    // Keep existing image for update if no new file selected
                    const existingImage = document.querySelector('tr[data-product-id="' + document.getElementById('productId').value + '"] img').src;
                    productImage.value = existingImage.split('/').pop();
                }
            } else {
                productImage.value = imageUrl.value || 'demo-product.jpg';
            }
        });
    </script>
</body>
</html>