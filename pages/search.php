<?php
require_once '../config/config.php';
require_once '../classes/Product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

// Get filter parameters
$filters = [
    'search' => $_GET['search'] ?? '',
    'category' => $_GET['category'] ?? '',
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'min_rating' => $_GET['min_rating'] ?? '',
    'in_stock' => isset($_GET['in_stock']) ? 1 : 0,
    'sort' => $_GET['sort'] ?? 'newest'
];

$products = $product->getAllProducts($filters);
$priceRange = $product->getPriceRange();

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
    <title>Advanced Search - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Advanced Search</h1>
            <p class="text-gray-600">Find exactly what you're looking for</p>
        </div>
        
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Advanced Filters Sidebar -->
            <div class="lg:w-1/4">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">
                        <i class="fas fa-filter mr-2"></i>Advanced Filters
                    </h2>
                    <form method="GET" class="space-y-6">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Keywords</label>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   placeholder="Product name or description...">
                        </div>
                        
                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $filters['category'] == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo $cat['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                            <div class="flex gap-2">
                                <input type="number" name="min_price" value="<?php echo $filters['min_price']; ?>" 
                                       class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       placeholder="Min" min="0" step="0.01">
                                <input type="number" name="max_price" value="<?php echo $filters['max_price']; ?>" 
                                       class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       placeholder="Max" min="0" step="0.01">
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Range: <?php echo formatPrice($priceRange['min_price']); ?> - <?php echo formatPrice($priceRange['max_price']); ?>
                            </div>
                        </div>
                        
                        <!-- Rating -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Rating</label>
                            <select name="min_rating" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Any Rating</option>
                                <option value="4" <?php echo $filters['min_rating'] == '4' ? 'selected' : ''; ?>>4+ Stars</option>
                                <option value="3" <?php echo $filters['min_rating'] == '3' ? 'selected' : ''; ?>>3+ Stars</option>
                                <option value="2" <?php echo $filters['min_rating'] == '2' ? 'selected' : ''; ?>>2+ Stars</option>
                                <option value="1" <?php echo $filters['min_rating'] == '1' ? 'selected' : ''; ?>>1+ Stars</option>
                            </select>
                        </div>
                        
                        <!-- Availability -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="in_stock" value="1" <?php echo $filters['in_stock'] ? 'checked' : ''; ?> 
                                       class="mr-2 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700">In Stock Only</span>
                            </label>
                        </div>
                        
                        <!-- Sort -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                            <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="newest" <?php echo $filters['sort'] == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                <option value="price_low" <?php echo $filters['sort'] == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $filters['sort'] == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="rating" <?php echo $filters['sort'] == 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                                <option value="popular" <?php echo $filters['sort'] == 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                                <option value="name" <?php echo $filters['sort'] == 'name' ? 'selected' : ''; ?>>Name A-Z</option>
                            </select>
                        </div>
                        
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                                <i class="fas fa-search mr-2"></i>Search
                            </button>
                            <a href="search.php" class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Search Results -->
            <div class="lg:w-3/4">
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Search Results</h2>
                        <p class="text-gray-600"><?php echo count($products); ?> products found</p>
                    </div>
                </div>
                
                <?php if (empty($products)): ?>
                    <div class="bg-white rounded-lg shadow-md p-12 text-center">
                        <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No products found</h3>
                        <p class="text-gray-500 mb-4">Try adjusting your search criteria or browse our categories</p>
                        <a href="../index.php" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                            Browse All Products
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <?php foreach ($products as $prod): ?>
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                                <div class="relative">
                                    <img src="../assets/images/<?php echo $prod['image']; ?>" 
                                         alt="<?php echo htmlspecialchars($prod['name']); ?>" 
                                         class="w-full h-48 object-cover"
                                         onerror="this.src='../assets/images/demo-product.jpg'">
                                    <div class="absolute top-2 right-2">
                                        <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                            <?php echo $prod['category_name']; ?>
                                        </span>
                                    </div>
                                    <?php if ($prod['stock'] == 0): ?>
                                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                            <span class="bg-red-500 text-white px-3 py-1 rounded-full font-semibold">Out of Stock</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($prod['name']); ?></h3>
                                    <p class="text-gray-600 text-sm mb-3"><?php echo substr($prod['description'], 0, 100); ?>...</p>
                                    
                                    <div class="flex items-center mb-3">
                                        <?php 
                                        $rating = round($prod['avg_rating']);
                                        for ($i = 1; $i <= 5; $i++): 
                                        ?>
                                            <i class="fas fa-star <?php echo $i <= $rating ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ml-2 text-sm text-gray-600">(<?php echo $prod['review_count']; ?>)</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-2xl font-bold text-blue-600"><?php echo formatPrice($prod['price']); ?></span>
                                        <span class="text-sm text-gray-500">Stock: <?php echo $prod['stock']; ?></span>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <a href="product.php?id=<?php echo $prod['id']; ?>" 
                                           class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition">
                                            <i class="fas fa-eye mr-2"></i>View Details
                                        </a>
                                        <?php if (isset($_SESSION['user_id']) && $prod['stock'] > 0): ?>
                                            <button class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition add-to-cart" 
                                                    data-product-id="<?php echo $prod['id']; ?>">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>