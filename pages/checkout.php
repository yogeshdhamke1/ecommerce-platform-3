<?php
require_once '../config/config.php';
require_once '../classes/Cart.php';
require_once '../classes/Order.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);
$order = new Order($db);

$cart_items = $cart->getCartItems($_SESSION['user_id']);
$total = $cart->getCartTotal($_SESSION['user_id']);

if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

if ($_POST) {
    $shipping_address = $_POST['shipping_address'];
    $order_id = $order->createOrder($_SESSION['user_id'], $total, $shipping_address, $_SESSION['currency']);
    
    if ($order_id) {
        header("Location: order_success.php?order_id=" . $order_id);
        exit();
    } else {
        $error = "Order creation failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="../index.php" class="hover:text-blue-600">Home</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li><a href="cart.php" class="hover:text-blue-600">Cart</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-blue-600 font-medium">Checkout</li>
            </ol>
        </nav>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-8">
            <i class="fas fa-credit-card mr-3 text-blue-600"></i>Checkout
        </h1>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-shipping-fast mr-2 text-blue-600"></i>Shipping Information
                    </h2>
                    
                    <form method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" id="full_name" name="full_name" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" id="phone" name="phone" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                      id="address" name="address" rows="3" required 
                                      placeholder="Street address, apartment, suite, etc."></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input type="text" id="city" name="city" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                                <input type="text" id="state" name="state" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="pincode" class="block text-sm font-medium text-gray-700 mb-2">Pincode</label>
                                <input type="text" id="pincode" name="pincode" required pattern="[1-9][0-9]{5}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       onchange="calculateShipping()">
                            </div>
                        </div>
                        
                        <div id="shippingOptions" class="hidden">
                            <h3 class="text-lg font-medium text-gray-800 mb-4">Shipping Options</h3>
                            <div id="shippingRates" class="space-y-3"></div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-4">Payment Method</h3>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <input class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" 
                                           type="radio" name="payment_method" id="credit_card" value="credit_card" checked>
                                    <label class="ml-3 block text-sm font-medium text-gray-700" for="credit_card">
                                        <i class="fas fa-credit-card mr-2 text-blue-600"></i>Credit Card
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" 
                                           type="radio" name="payment_method" id="paypal" value="paypal">
                                    <label class="ml-3 block text-sm font-medium text-gray-700" for="paypal">
                                        <i class="fab fa-paypal mr-2 text-blue-600"></i>PayPal
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 font-medium transition">
                            <i class="fas fa-lock mr-2"></i>Place Order
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-receipt mr-2 text-blue-600"></i>Order Summary
                    </h2>
                    
                    <div class="space-y-4 mb-6">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <div>
                                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($item['name']); ?></p>
                                    <p class="text-sm text-gray-600">Qty: <?php echo $item['quantity']; ?></p>
                                </div>
                                <span class="font-semibold text-gray-900"><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold"><?php echo formatPrice($total); ?></span>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-600">Shipping</span>
                            <span id="shippingCost" class="font-medium">Calculate</span>
                        </div>
                        <div class="flex justify-between items-center text-lg font-bold text-gray-900">
                            <span>Total</span>
                            <span class="text-blue-600"><?php echo formatPrice($total); ?></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Currency: <?php echo $_SESSION['currency']; ?></p>
                    </div>
                    
                    <div class="mt-6 text-center">
                        <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                            <i class="fas fa-shield-alt text-green-500"></i>
                            <span>Secure SSL Encryption</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script src="../assets/js/main.js"></script>
    <script>
        let selectedShippingRate = 0;
        
        function calculateShipping() {
            const pincode = document.getElementById('pincode').value;
            const shippingOptions = document.getElementById('shippingOptions');
            const shippingRates = document.getElementById('shippingRates');
            
            if (!/^[1-9][0-9]{5}$/.test(pincode)) {
                shippingOptions.classList.add('hidden');
                return;
            }
            
            shippingRates.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Calculating shipping rates...</div>';
            shippingOptions.classList.remove('hidden');
            
            const formData = new FormData();
            formData.append('pincode', pincode);
            formData.append('weight', 1);
            formData.append('city', document.getElementById('city').value);
            formData.append('state', document.getElementById('state').value);
            
            fetch('shipping_calculator.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let html = '';
                    data.rates.forEach((rate, index) => {
                        html += `
                            <div class="border border-gray-200 rounded-md p-3 hover:bg-gray-50 transition">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="shipping_method" value="${rate.method}" 
                                           data-rate="${rate.rate}" class="mr-3" ${index === 0 ? 'checked' : ''}
                                           onchange="updateShippingCost(${rate.rate})">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">${rate.method}</div>
                                        <div class="text-sm text-gray-600">${rate.description}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-truck mr-1"></i>${rate.carrier} • ${rate.estimate}
                                        </div>
                                    </div>
                                    <div class="font-bold text-blue-600">${rate.formatted_rate}</div>
                                </label>
                            </div>
                        `;
                    });
                    shippingRates.innerHTML = html;
                    
                    // Set default shipping cost
                    if (data.rates.length > 0) {
                        updateShippingCost(data.rates[0].rate);
                    }
                } else {
                    shippingRates.innerHTML = '<div class="text-red-600 text-sm">Unable to calculate shipping rates for this pincode</div>';
                }
            })
            .catch(error => {
                shippingRates.innerHTML = '<div class="text-red-600 text-sm">Network error. Please try again.</div>';
            });
        }
        
        function updateShippingCost(rate) {
            selectedShippingRate = rate;
            const shippingCost = document.getElementById('shippingCost');
            const subtotal = <?php echo $total; ?>;
            
            if (rate > 0) {
                shippingCost.innerHTML = '<?php echo $_SESSION["currency"] === "INR" ? "₹" : "$"; ?>' + rate.toFixed(2);
                shippingCost.className = 'font-medium text-gray-900';
            } else {
                shippingCost.innerHTML = 'Free';
                shippingCost.className = 'font-medium text-green-600';
            }
            
            // Update total
            const total = subtotal + rate;
            document.querySelector('.text-blue-600').textContent = '<?php echo $_SESSION["currency"] === "INR" ? "₹" : "$"; ?>' + total.toFixed(2);
        }
    </script>
</body>
</html>