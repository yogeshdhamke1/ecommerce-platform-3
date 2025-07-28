<?php
require_once '../config/config.php';
require_once '../classes/Cart.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);

$cart_items = $cart->getCartItems($_SESSION['user_id']);
$total = $cart->getCartTotal($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Review your shopping cart items and proceed to secure checkout.">
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="../index.php" class="hover:text-blue-600 transition">Home</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-blue-600 font-medium">Shopping Cart</li>
            </ol>
        </nav>
        
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-shopping-cart mr-3 text-blue-600"></i>Shopping Cart
            </h1>
            <a href="../index.php" class="text-blue-600 hover:text-blue-700 transition font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
            </a>
        </div>
        
        <?php if (empty($cart_items)): ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <div class="mb-6">
                    <i class="fas fa-shopping-cart text-6xl text-gray-300"></i>
                </div>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Your cart is empty</h2>
                <p class="text-gray-500 mb-8">Looks like you haven't added any items to your cart yet.</p>
                <a href="../index.php" class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-shopping-bag mr-2"></i>Start Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-4">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                            <div class="flex flex-col md:flex-row gap-6">
                                <div class="md:w-32 md:h-32 flex-shrink-0">
                                    <img src="../assets/images/<?php echo $item['image']; ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         class="w-full h-32 object-cover rounded-md"
                                         onerror="this.src='../assets/images/demo-product.jpg'">
                                </div>
                                
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($item['name']); ?></h3>
                                        <button class="text-red-500 hover:text-red-700 transition remove-item" 
                                                data-product-id="<?php echo $item['product_id']; ?>" 
                                                title="Remove item">
                                            <i class="fas fa-trash text-lg"></i>
                                        </button>
                                    </div>
                                    
                                    <p class="text-gray-600 mb-4">Price: <span class="font-semibold text-blue-600"><?php echo formatPrice($item['price']); ?></span></p>
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <label class="text-sm font-medium text-gray-700">Quantity:</label>
                                            <div class="flex items-center border border-gray-300 rounded-md">
                                                <button class="px-3 py-1 text-gray-600 hover:text-gray-800 transition" onclick="decreaseQuantity(<?php echo $item['product_id']; ?>)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" 
                                                       class="w-16 text-center border-0 focus:outline-none quantity-input" 
                                                       value="<?php echo $item['quantity']; ?>" 
                                                       min="1" 
                                                       data-product-id="<?php echo $item['product_id']; ?>">
                                                <button class="px-3 py-1 text-gray-600 hover:text-gray-800 transition" onclick="increaseQuantity(<?php echo $item['product_id']; ?>)">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">Subtotal</p>
                                            <p class="text-xl font-bold text-gray-900"><?php echo formatPrice($item['price'] * $item['quantity']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">
                            <i class="fas fa-receipt mr-2 text-blue-600"></i>Order Summary
                        </h2>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Items (<?php echo count($cart_items); ?>)</span>
                                <span><?php echo formatPrice($total); ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span class="text-green-600 font-medium">Free</span>
                            </div>
                            <div class="border-t pt-4">
                                <div class="flex justify-between text-lg font-bold text-gray-900">
                                    <span>Total</span>
                                    <span class="text-blue-600"><?php echo formatPrice($total); ?></span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Currency: <?php echo $_SESSION['currency']; ?></p>
                            </div>
                        </div>
                        
                        <a href="checkout.php" class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 transition font-medium text-center block">
                            <i class="fas fa-lock mr-2"></i>Secure Checkout
                        </a>
                        
                        <div class="mt-4 text-center">
                            <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                                <i class="fas fa-shield-alt text-green-500"></i>
                                <span>Secure SSL Encryption</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script src="../assets/js/main.js"></script>
    <script>
        function increaseQuantity(productId) {
            const input = document.querySelector(`input[data-product-id="${productId}"]`);
            input.value = parseInt(input.value) + 1;
            input.dispatchEvent(new Event('change'));
        }
        
        function decreaseQuantity(productId) {
            const input = document.querySelector(`input[data-product-id="${productId}"]`);
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                input.dispatchEvent(new Event('change'));
            }
        }
    </script>
</body>
</html>