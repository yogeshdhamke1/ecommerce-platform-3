<?php
// Determine the correct path based on current location
$current_path = $_SERVER['REQUEST_URI'];
$is_in_pages = strpos($current_path, '/pages/') !== false;
$base_path = $is_in_pages ? '' : 'pages/';

// Get database connection for cart count
if (!isset($GLOBALS['db']) && isset($_SESSION['user_id'])) {
    require_once ($is_in_pages ? '../' : '') . 'config/database.php';
    $database = new Database();
    $GLOBALS['db'] = $database->getConnection();
}
?>
<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="<?php echo BASE_URL; ?>" class="text-2xl font-bold text-blue-600 hover:text-blue-700 transition">
                    <i class="fas fa-store mr-2"></i><?php echo SITE_NAME; ?>
                </a>
            </div>
            
            <!-- Search Bar -->
            <div class="hidden md:flex flex-1 max-w-lg mx-8">
                <div class="relative w-full">
                    <form action="<?php echo $is_in_pages ? '' : 'pages/'; ?>search.php" method="GET" class="flex">
                        <input type="text" name="search" id="searchInput" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="Search products..." autocomplete="off">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700 transition">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <div id="searchSuggestions" class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-b-md shadow-lg z-50 hidden"></div>
                </div>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-6">
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
                
                <a href="<?php echo $base_path; ?>search.php" class="text-gray-700 hover:text-blue-600 transition font-medium">
                    <i class="fas fa-search-plus mr-1"></i>Advanced Search
                </a>
            </div>
            
            <!-- Right Side Navigation -->
            <div class="flex items-center space-x-4">

                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $base_path; ?>cart.php" class="text-gray-700 hover:text-blue-600 transition relative">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <?php
                        if (isset($_SESSION['user_id'])) {
                            $cart_query = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
                            $cart_stmt = $GLOBALS['db']->prepare($cart_query);
                            $cart_stmt->execute([$_SESSION['user_id']]);
                            $cart_count = $cart_stmt->fetchColumn();
                            if ($cart_count > 0) {
                                echo '<span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">' . $cart_count . '</span>';
                            }
                        }
                        ?>
                    </a>
                    <a href="<?php echo $base_path; ?>wishlist.php" class="text-gray-700 hover:text-blue-600 transition">
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
                            <a href="<?php echo $base_path; ?>dashboard.php" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <a href="<?php echo $base_path; ?>profile.php" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <a href="<?php echo $base_path; ?>orders.php" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-box mr-2"></i>Orders
                            </a>
                            <hr class="my-1">
                            <a href="<?php echo $base_path; ?>logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-50 transition">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo $base_path; ?>login.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-sign-in-alt mr-1"></i>Login
                    </a>
                    <a href="<?php echo $base_path; ?>register.php" class="border border-blue-600 text-blue-600 px-4 py-2 rounded-md hover:bg-blue-600 hover:text-white transition">
                        <i class="fas fa-user-plus mr-1"></i>Register
                    </a>
                <?php endif; ?>
                
                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" class="lg:hidden text-gray-700 hover:text-blue-600 transition">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Search -->
        <div class="md:hidden mt-4">
            <form action="<?php echo $is_in_pages ? '' : 'pages/'; ?>search.php" method="GET" class="flex">
                <input type="text" name="search" 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       placeholder="Search products...">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700 transition">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="lg:hidden hidden border-t border-gray-200 py-4">
            <div class="space-y-2">
                <a href="<?php echo BASE_URL; ?>" class="block py-2 text-gray-700 hover:text-blue-600 transition">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
                <a href="<?php echo $base_path; ?>search.php" class="block py-2 text-gray-700 hover:text-blue-600 transition">
                    <i class="fas fa-search-plus mr-2"></i>Advanced Search
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
    
    // Search autocomplete
    const searchInput = document.getElementById('searchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                searchSuggestions.classList.add('hidden');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`<?php echo $is_in_pages ? '' : 'pages/'; ?>search_api.php?q=${encodeURIComponent(query)}&limit=5`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            let html = '';
                            data.forEach(item => {
                                html += `
                                    <a href="<?php echo $is_in_pages ? '' : 'pages/'; ?>product.php?id=${item.id}" class="flex items-center p-3 hover:bg-gray-50 border-b border-gray-100">
                                        <img src="<?php echo $is_in_pages ? '../' : ''; ?>assets/images/${item.image}" alt="${item.name}" class="w-10 h-10 object-cover rounded mr-3" onerror="this.src='<?php echo $is_in_pages ? '../' : ''; ?>assets/images/demo-product.jpg'">
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">${item.name}</div>
                                            <div class="text-sm text-gray-500">${item.category} • ${item.price}</div>
                                        </div>
                                    </a>
                                `;
                            });
                            searchSuggestions.innerHTML = html;
                            searchSuggestions.classList.remove('hidden');
                        } else {
                            searchSuggestions.classList.add('hidden');
                        }
                    })
                    .catch(() => {
                        searchSuggestions.classList.add('hidden');
                    });
            }, 300);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                searchSuggestions.classList.add('hidden');
            }
        });
    }
</script>