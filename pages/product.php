<?php
require_once '../config/config.php';
require_once '../classes/Product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$product_details = $product->getProductById($_GET['id']);
if (!$product_details) {
    header("Location: ../index.php");
    exit();
}

// Handle review submission
if ($_POST && isset($_SESSION['user_id'])) {
    $query = "INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    if ($stmt->execute([$_SESSION['user_id'], $_GET['id'], $_POST['rating'], $_POST['comment']])) {
        $success = "Review submitted successfully!";
    }
}

// Get reviews
$reviews_query = "SELECT r.*, u.full_name FROM reviews r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.product_id = ? ORDER BY r.created_at DESC";
$reviews_stmt = $db->prepare($reviews_query);
$reviews_stmt->execute([$_GET['id']]);
$reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate average rating
$avg_query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id = ?";
$avg_stmt = $db->prepare($avg_query);
$avg_stmt->execute([$_GET['id']]);
$rating_data = $avg_stmt->fetch(PDO::FETCH_ASSOC);
$avg_rating = round($rating_data['avg_rating'], 1);
$total_reviews = $rating_data['total_reviews'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product_details['name']); ?> - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/zoom.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <nav class="mb-8">
            <a href="../index.php" class="text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-1"></i>Back to Products
            </a>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
            <!-- Product Image with Zoom -->
            <div class="relative">
                <div id="imageContainer" class="relative overflow-hidden rounded-lg shadow-md cursor-zoom-in">
                    <img id="productImage" 
                         src="../assets/images/<?php echo $product_details['image']; ?>" 
                         alt="<?php echo htmlspecialchars($product_details['name']); ?>"
                         class="w-full h-96 object-cover transition-transform duration-300"
                         onerror="this.src='../assets/images/demo-product.jpg'">
                    <div id="zoomLens" class="absolute border-2 border-white shadow-lg pointer-events-none opacity-0 transition-opacity duration-200"></div>
                    <div id="zoomResult" class="absolute top-4 right-4 w-32 h-32 border-2 border-white rounded-lg shadow-lg bg-white overflow-hidden opacity-0 transition-opacity duration-200 z-10"></div>
                </div>
                <button id="fullscreenBtn" class="absolute top-4 left-4 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition">
                    <i class="fas fa-expand text-sm"></i>
                </button>
            </div>
            
            <!-- Product Details -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($product_details['name']); ?></h1>
                
                <!-- Rating -->
                <div class="flex items-center mb-4">
                    <div class="flex items-center">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo $i <= $avg_rating ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="ml-2 text-gray-600"><?php echo $avg_rating; ?> (<?php echo $total_reviews; ?> reviews)</span>
                </div>
                
                <p class="text-3xl font-bold text-blue-600 mb-6"><?php echo formatPrice($product_details['price']); ?></p>
                
                <p class="text-gray-700 mb-6"><?php echo nl2br(htmlspecialchars($product_details['description'])); ?></p>
                
                <div class="mb-6">
                    <p class="text-sm text-gray-600">Category: <span class="font-medium"><?php echo $product_details['category_name']; ?></span></p>
                    <p class="text-sm text-gray-600">Stock: <span class="font-medium"><?php echo $product_details['stock']; ?> available</span></p>
                </div>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex space-x-4 mb-6">
                        <button class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 transition add-to-cart"
                                data-product-id="<?php echo $product_details['id']; ?>">
                            <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                        </button>
                        <button class="bg-red-500 text-white px-8 py-3 rounded-md hover:bg-red-600 transition">
                            <i class="fas fa-heart mr-2"></i>Add to Wishlist
                        </button>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600 mb-6">
                        <a href="login.php" class="text-blue-600 hover:text-blue-700">Login</a> to add to cart or wishlist
                    </p>
                <?php endif; ?>
                
                <!-- Shipping Calculator -->
                <div class="mb-6">
                    <?php
                    include '../includes/shipping_widget.php';
                    renderShippingWidget(1, $product_details['id']);
                    ?>
                </div>
                
                <!-- Social Share Buttons -->
                <div class="border-t pt-6">
                    <?php
                    require_once '../config/social_config.php';
                    include '../includes/social_buttons.php';
                    $product_url = BASE_URL . 'pages/product.php?id=' . $product_details['id'];
                    $product_image = BASE_URL . 'assets/images/' . $product_details['image'];
                    renderSocialShareButtons($product_url, $product_details['name'], $product_details['description'], $product_image);
                    ?>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Customer Reviews</h2>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Add Review Form -->
                <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Write a Review</h3>
                    
                    <?php if (isset($success)): ?>
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                            <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                            <div class="flex space-x-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" class="hidden" required>
                                    <label for="star<?php echo $i; ?>" class="cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 star-rating">
                                        <i class="fas fa-star"></i>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                            <textarea name="comment" rows="4" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Share your experience with this product..."></textarea>
                        </div>
                        
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Review
                        </button>
                    </form>
                </div>
            <?php endif; ?>
            
            <!-- Reviews List -->
            <?php if (empty($reviews)): ?>
                <p class="text-gray-500 text-center py-8">No reviews yet. Be the first to review this product!</p>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($reviews as $review): ?>
                        <div class="border-b border-gray-200 pb-6">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 rounded-full p-2 mr-3">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($review['full_name']); ?></p>
                                        <div class="flex items-center">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?> text-sm"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-500"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                            </div>
                            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Related Products -->
        <?php
        require_once '../classes/Recommendations.php';
        $recommendations = new Recommendations($db);
        $related_products = $recommendations->getRelatedProducts($_GET['id'], 4);
        if (!empty($related_products)):
        ?>
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Products</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($related_products as $product): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <img src="../assets/images/<?php echo $product['image']; ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             class="w-full h-48 object-cover"
                             onerror="this.src='../assets/images/demo-product.jpg'">
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="text-blue-600 font-bold mb-3"><?php echo formatPrice($product['price']); ?></p>
                            <a href="product.php?id=<?php echo $product['id']; ?>" 
                               class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition text-center block">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <!-- Fullscreen Modal -->
    <div id="fullscreenModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center">
        <div class="relative max-w-4xl max-h-full p-4">
            <img id="fullscreenImage" src="" alt="" class="max-w-full max-h-full object-contain">
            <button id="closeModal" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300 transition">
                <i class="fas fa-times"></i>
            </button>
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-4">
                <button id="zoomIn" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded hover:bg-opacity-30 transition">
                    <i class="fas fa-plus mr-2"></i>Zoom In
                </button>
                <button id="zoomOut" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded hover:bg-opacity-30 transition">
                    <i class="fas fa-minus mr-2"></i>Zoom Out
                </button>
                <button id="resetZoom" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded hover:bg-opacity-30 transition">
                    <i class="fas fa-undo mr-2"></i>Reset
                </button>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/mobile-zoom.js"></script>
    <script>
        // Image Zoom Functionality
        class ImageZoom {
            constructor() {
                this.container = document.getElementById('imageContainer');
                this.image = document.getElementById('productImage');
                this.lens = document.getElementById('zoomLens');
                this.result = document.getElementById('zoomResult');
                this.fullscreenBtn = document.getElementById('fullscreenBtn');
                this.modal = document.getElementById('fullscreenModal');
                this.fullscreenImage = document.getElementById('fullscreenImage');
                this.scale = 1;
                this.init();
            }
            
            init() {
                this.container.addEventListener('mouseenter', () => this.showZoom());
                this.container.addEventListener('mouseleave', () => this.hideZoom());
                this.container.addEventListener('mousemove', (e) => this.moveZoom(e));
                this.fullscreenBtn.addEventListener('click', () => this.openFullscreen());
                document.getElementById('closeModal').addEventListener('click', () => this.closeFullscreen());
                document.getElementById('zoomIn').addEventListener('click', () => this.zoomIn());
                document.getElementById('zoomOut').addEventListener('click', () => this.zoomOut());
                document.getElementById('resetZoom').addEventListener('click', () => this.resetZoom());
                this.modal.addEventListener('click', (e) => {
                    if (e.target === this.modal) this.closeFullscreen();
                });
            }
            
            showZoom() {
                this.lens.style.opacity = '1';
                this.result.style.opacity = '1';
                this.result.style.backgroundImage = `url(${this.image.src})`;
                this.result.style.backgroundSize = `${this.container.offsetWidth * 3}px ${this.container.offsetHeight * 3}px`;
            }
            
            hideZoom() {
                this.lens.style.opacity = '0';
                this.result.style.opacity = '0';
            }
            
            moveZoom(e) {
                const rect = this.container.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const lensWidth = 100;
                const lensHeight = 100;
                
                let lensX = x - lensWidth / 2;
                let lensY = y - lensHeight / 2;
                
                if (lensX < 0) lensX = 0;
                if (lensY < 0) lensY = 0;
                if (lensX > rect.width - lensWidth) lensX = rect.width - lensWidth;
                if (lensY > rect.height - lensHeight) lensY = rect.height - lensHeight;
                
                this.lens.style.left = lensX + 'px';
                this.lens.style.top = lensY + 'px';
                this.lens.style.width = lensWidth + 'px';
                this.lens.style.height = lensHeight + 'px';
                
                const fx = (this.container.offsetWidth * 3) / this.container.offsetWidth;
                const fy = (this.container.offsetHeight * 3) / this.container.offsetHeight;
                
                this.result.style.backgroundPosition = `-${lensX * fx}px -${lensY * fy}px`;
            }
            
            openFullscreen() {
                this.fullscreenImage.src = this.image.src;
                this.modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                this.scale = 1;
                this.updateFullscreenZoom();
            }
            
            closeFullscreen() {
                this.modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
            
            zoomIn() {
                this.scale = Math.min(this.scale * 1.2, 3);
                this.updateFullscreenZoom();
            }
            
            zoomOut() {
                this.scale = Math.max(this.scale / 1.2, 0.5);
                this.updateFullscreenZoom();
            }
            
            resetZoom() {
                this.scale = 1;
                this.updateFullscreenZoom();
            }
            
            updateFullscreenZoom() {
                this.fullscreenImage.style.transform = `scale(${this.scale})`;
            }
        }
        
        // Initialize zoom when page loads
        document.addEventListener('DOMContentLoaded', () => {
            new ImageZoom();
        });
        
        // Star rating interaction
        document.querySelectorAll('.star-rating').forEach((star, index) => {
            star.addEventListener('click', function() {
                document.querySelectorAll('.star-rating').forEach((s, i) => {
                    if (i <= index) {
                        s.classList.remove('text-gray-300');
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-300');
                    }
                });
            });
        });
    </script>
</body>
</html>