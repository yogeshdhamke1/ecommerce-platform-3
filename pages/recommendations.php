<?php
require_once '../config/config.php';
require_once '../classes/Recommendations.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$recommendations = new Recommendations($db);

// Debug: Get all products first
$debug_query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LIMIT 12";
$debug_stmt = $db->prepare($debug_query);
$debug_stmt->execute();
$recommended_products = $debug_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommended for You - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-magic text-purple-600 mr-3"></i>Recommended for You
            </h1>
            <p class="text-gray-600">Products selected based on your shopping history and preferences</p>
        </div>
        
        <?php if (empty($recommended_products)): ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-lightbulb text-6xl text-gray-300 mb-6"></i>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">No Recommendations Yet</h2>
                <p class="text-gray-500 mb-8">Start shopping to get personalized product recommendations!</p>
                <a href="../index.php" class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 transition">
                    <i class="fas fa-shopping-bag mr-2"></i>Browse Products
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($recommended_products as $product): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <div class="relative">
                            <img src="../assets/images/<?php echo $product['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 class="w-full h-48 object-cover"
                                 onerror="this.src='../assets/images/demo-product.jpg'">
                            <div class="absolute top-2 right-2">
                                <span class="bg-purple-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                    Recommended
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="text-sm text-gray-600 mb-2"><?php echo $product['category_name']; ?></p>
                            <p class="text-blue-600 font-bold mb-4"><?php echo formatPrice($product['price']); ?></p>
                            <div class="flex gap-2">
                                <a href="product.php?id=<?php echo $product['id']; ?>" 
                                   class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition">
                                    View Details
                                </a>
                                <button class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition add-to-cart" 
                                        data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>