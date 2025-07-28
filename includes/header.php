<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="<?php echo BASE_URL; ?>" class="text-2xl font-bold text-blue-600 hover:text-blue-700 transition">
                    <i class="fas fa-store mr-2"></i><?php echo SITE_NAME; ?>
                </a>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-8">
                <a href="<?php echo BASE_URL; ?>" class="text-gray-700 hover:text-blue-600 transition font-medium">
                    <i class="fas fa-home mr-1"></i>Home
                </a>
                
                <!-- Categories Dropdown -->
                <div class="relative group">
                    <button class="text-gray-700 hover:text-blue-600 transition font-medium flex items-center">
                        <i class="fas fa-th-large mr-1"></i>Categories <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute top-full left-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <a href="<?php echo BASE_URL; ?>?category=1" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                            <i class="fas fa-gem mr-2"></i>Jewelry
                        </a>
                        <a href="<?php echo BASE_URL; ?>?category=2" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                            <i class="fas fa-laptop mr-2"></i>Electronics
                        </a>
                        <a href="<?php echo BASE_URL; ?>?category=3" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                            <i class="fas fa-tshirt mr-2"></i>Fashion
                        </a>
                        <a href="<?php echo BASE_URL; ?>?category=4" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                            <i class="fas fa-home mr-2"></i>Home & Garden
                        </a>
                        <a href="<?php echo BASE_URL; ?>?category=5" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                            <i class="fas fa-football-ball mr-2"></i>Sports
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Right Side Navigation -->
            <div class="flex items-center space-x-4">
                <!-- Currency Selector -->
                <div class="relative group">
                    <button class="text-gray-700 hover:text-blue-600 transition font-medium flex items-center">
                        <i class="fas fa-globe mr-1"></i><?php echo $_SESSION['currency']; ?> <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute top-full right-0 mt-2 w-32 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <a href="#" class="currency-select block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition" data-currency="INR">INR (₹)</a>
                        <a href="#" class="currency-select block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition" data-currency="USD">USD ($)</a>
                        <a href="#" class="currency-select block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition" data-currency="EUR">EUR (€)</a>
                        <a href="#" class="currency-select block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition" data-currency="GBP">GBP (£)</a>
                        <a href="#" class="currency-select block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition" data-currency="JPY">JPY (¥)</a>
                    </div>
                </div>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="pages/cart.php" class="text-gray-700 hover:text-blue-600 transition relative">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                    </a>
                    <a href="pages/wishlist.php" class="text-gray-700 hover:text-blue-600 transition">
                        <i class="fas fa-heart text-xl"></i>
                    </a>
                    
                    <!-- User Dropdown -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 transition">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span class="hidden md:block"><?php echo $_SESSION['username']; ?></span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute top-full right-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <a href="pages/dashboard.php" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <a href="pages/profile.php" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <a href="pages/orders.php" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-box mr-2"></i>Orders
                            </a>
                            <hr class="my-1">
                            <a href="pages/logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-50 transition">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="pages/login.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-sign-in-alt mr-1"></i>Login
                    </a>
                    <a href="pages/register.php" class="border border-blue-600 text-blue-600 px-4 py-2 rounded-md hover:bg-blue-600 hover:text-white transition">
                        <i class="fas fa-user-plus mr-1"></i>Register
                    </a>
                <?php endif; ?>
                
                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" class="lg:hidden text-gray-700 hover:text-blue-600 transition">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="lg:hidden hidden border-t border-gray-200 py-4">
            <div class="space-y-2">
                <a href="<?php echo BASE_URL; ?>" class="block py-2 text-gray-700 hover:text-blue-600 transition">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
                <div class="py-2">
                    <span class="text-gray-700 font-medium"><i class="fas fa-th-large mr-2"></i>Categories</span>
                    <div class="ml-6 mt-2 space-y-1">
                        <a href="<?php echo BASE_URL; ?>?category=1" class="block py-1 text-gray-600 hover:text-blue-600 transition">Jewelry</a>
                        <a href="<?php echo BASE_URL; ?>?category=2" class="block py-1 text-gray-600 hover:text-blue-600 transition">Electronics</a>
                        <a href="<?php echo BASE_URL; ?>?category=3" class="block py-1 text-gray-600 hover:text-blue-600 transition">Fashion</a>
                        <a href="<?php echo BASE_URL; ?>?category=4" class="block py-1 text-gray-600 hover:text-blue-600 transition">Home & Garden</a>
                        <a href="<?php echo BASE_URL; ?>?category=5" class="block py-1 text-gray-600 hover:text-blue-600 transition">Sports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobileMenuBtn').addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.classList.toggle('hidden');
    });
</script>