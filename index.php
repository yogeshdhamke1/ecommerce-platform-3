<?php
require_once 'config/config.php';
require_once 'classes/Product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

// Build filters array for backward compatibility
$filters = [
    'category' => $_GET['category'] ?? '',
    'search' => $_GET['search'] ?? ''
];
$products = $product->getAllProducts($filters);

// Get categories for filter
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
    <title><?php echo SITE_NAME; ?> - Premium E-Commerce Store</title>
    <meta name="description" content="Shop premium products across jewelry, electronics, fashion, home & garden, and sports. Multi-currency support with secure checkout.">
    <meta name="keywords" content="ecommerce, online shopping, jewelry, electronics, fashion, home decor, sports equipment">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="<?php echo SITE_NAME; ?> - Premium E-Commerce Store">
    <meta property="og:description" content="Shop premium products with multi-currency support and secure checkout">
    <meta property="og:type" content="website">
    <link rel="canonical" href="<?php echo BASE_URL; ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#64748b'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>
    
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold mb-4">Premium E-Commerce Store</h1>
            <p class="text-xl mb-8">Discover amazing products with multi-currency support</p>
            <a href="#products" class="bg-white text-blue-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition">
                Shop Now <i class="fas fa-arrow-down ml-2"></i>
            </a>
        </div>
    </section>

    <div class="container mx-auto px-4 py-8" id="products">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:w-1/4">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Filters</h2>
                    <form method="GET" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Products</label>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   placeholder="Search...">
                        </div>
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
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                        <a href="pages/search.php" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition text-center block">
                            <i class="fas fa-search-plus mr-2"></i>Advanced Search
                        </a>
                    </form>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="lg:w-3/4">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Featured Products</h2>
                    <p class="text-gray-600">Discover our premium collection</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($products as $prod): ?>
                        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="relative">
                                <img src="assets/images/<?php echo $prod['image']; ?>" 
                                     alt="<?php echo htmlspecialchars($prod['name']); ?>" 
                                     class="w-full h-48 object-cover"
                                     onerror="this.src='assets/images/demo-product.jpg'">
                                <div class="absolute top-2 right-2">
                                    <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                        <?php echo $prod['category_name']; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($prod['name']); ?></h3>
                                <p class="text-gray-600 text-sm mb-3"><?php echo substr($prod['description'], 0, 100); ?>...</p>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-2xl font-bold text-blue-600"><?php echo formatPrice($prod['price']); ?></span>
                                    <span class="text-sm text-gray-500">Stock: <?php echo $prod['stock']; ?></span>
                                </div>
                                <div class="flex gap-2">
                                    <a href="pages/product.php?id=<?php echo $prod['id']; ?>" 
                                       class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition">
                                        <i class="fas fa-eye mr-2"></i>View Details
                                    </a>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <button class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition add-to-cart" 
                                                data-product-id="<?php echo $prod['id']; ?>">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                        <button class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition add-to-wishlist" 
                                                data-product-id="<?php echo $prod['id']; ?>">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Recommended Products -->
        <?php
        require_once 'classes/Recommendations.php';
        $recommendations = new Recommendations($db);
        if (isset($_SESSION['user_id'])) {
            $recommended_products = $recommendations->getRecommendedProducts($_SESSION['user_id'], 8);
        } else {
            $recommended_products = $recommendations->getPopularProducts(8);
        }
        if (!empty($recommended_products)):
        ?>
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">
                <?php echo isset($_SESSION['user_id']) ? 'Recommended for You' : 'Popular Products'; ?>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($recommended_products as $product): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <div class="relative">
                            <img src="assets/images/<?php echo $product['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 class="w-full h-48 object-cover"
                                 onerror="this.src='assets/images/demo-product.jpg'">
                            <div class="absolute top-2 right-2">
                                <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                    <?php echo $product['category_name']; ?>
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="text-blue-600 font-bold mb-3"><?php echo formatPrice($product['price']); ?></p>
                            <div class="flex gap-2">
                                <a href="pages/product.php?id=<?php echo $product['id']; ?>" 
                                   class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition">
                                    View Details
                                </a>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <button class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition add-to-cart" 
                                            data-product-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
    <!-- Scroll to Top Button -->
    <button id="scrollTop" class="fixed bottom-6 right-6 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition opacity-0 pointer-events-none">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <script>
        // Scroll to top functionality
        window.addEventListener('scroll', function() {
            const scrollTop = document.getElementById('scrollTop');
            if (window.pageYOffset > 300) {
                scrollTop.classList.remove('opacity-0', 'pointer-events-none');
            } else {
                scrollTop.classList.add('opacity-0', 'pointer-events-none');
            }
        });
        
        document.getElementById('scrollTop').addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>