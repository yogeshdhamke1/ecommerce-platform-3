<?php
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get wishlist items
$query = "SELECT w.*, p.name, p.price, p.image, p.stock FROM wishlist w 
          JOIN products p ON w.product_id = p.id 
          WHERE w.user_id = ? ORDER BY w.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-heart text-red-500 mr-3"></i>My Wishlist
            </h1>
            <p class="text-gray-600">Save your favorite items for later</p>
        </div>
        
        <?php if (empty($wishlist_items)): ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-heart text-6xl text-gray-300 mb-6"></i>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Your wishlist is empty</h2>
                <p class="text-gray-500 mb-8">Start adding items you love to your wishlist</p>
                <a href="../index.php" class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 transition">
                    <i class="fas fa-shopping-bag mr-2"></i>Browse Products
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($wishlist_items as $item): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <div class="relative">
                            <img src="../assets/images/<?php echo $item['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 class="w-full h-48 object-cover"
                                 onerror="this.src='../assets/images/demo-product.jpg'">
                            <button class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition remove-wishlist"
                                    data-product-id="<?php echo $item['product_id']; ?>">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="text-2xl font-bold text-blue-600 mb-4"><?php echo formatPrice($item['price']); ?></p>
                            
                            <div class="flex space-x-2">
                                <button class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition add-to-cart"
                                        data-product-id="<?php echo $item['product_id']; ?>">
                                    <i class="fas fa-cart-plus mr-1"></i>Add to Cart
                                </button>
                                <a href="product.php?id=<?php echo $item['product_id']; ?>" 
                                   class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script src="../assets/js/main.js"></script>
    <script>
        // Remove from wishlist
        document.querySelectorAll('.remove-wishlist').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                
                fetch('remove_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({product_id: productId})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            });
        });
    </script>
</body>
</html>