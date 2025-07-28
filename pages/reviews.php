<?php
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get user reviews
$query = "SELECT r.*, p.name as product_name, p.image FROM reviews r 
          JOIN products p ON r.product_id = p.id 
          WHERE r.user_id = ? ORDER BY r.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reviews - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-star text-yellow-500 mr-3"></i>My Reviews
            </h1>
            <p class="text-gray-600">Your product reviews and ratings</p>
        </div>
        
        <?php if (empty($reviews)): ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-star text-6xl text-gray-300 mb-6"></i>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">No reviews yet</h2>
                <p class="text-gray-500 mb-8">Share your experience with products you've purchased</p>
                <a href="orders.php" class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 transition">
                    <i class="fas fa-box mr-2"></i>View Orders
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($reviews as $review): ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-start space-x-4">
                            <img src="../assets/images/<?php echo $review['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($review['product_name']); ?>"
                                 class="w-16 h-16 object-cover rounded-md"
                                 onerror="this.src='../assets/images/demo-product.jpg'">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($review['product_name']); ?></h3>
                                    <span class="text-sm text-gray-500"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                                </div>
                                
                                <div class="flex items-center mb-3">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                                    <?php endfor; ?>
                                    <span class="ml-2 text-sm text-gray-600"><?php echo $review['rating']; ?>/5</span>
                                </div>
                                
                                <?php if ($review['comment']): ?>
                                    <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>