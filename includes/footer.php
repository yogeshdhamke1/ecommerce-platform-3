<footer class="bg-gray-900 text-white mt-16">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div>
                <div class="flex items-center mb-4">
                    <i class="fas fa-store text-2xl text-blue-400 mr-2"></i>
                    <h3 class="text-xl font-bold"><?php echo SITE_NAME; ?></h3>
                </div>
                <p class="text-gray-300 mb-4">Your trusted e-commerce platform with multi-currency support and secure shopping experience.</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition">
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition">
                        <i class="fab fa-linkedin-in text-xl"></i>
                    </a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="<?php echo BASE_URL; ?>" class="text-gray-300 hover:text-blue-400 transition flex items-center">
                        <i class="fas fa-home mr-2"></i>Home
                    </a></li>
                    <li><a href="pages/about.php" class="text-gray-300 hover:text-blue-400 transition flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>About Us
                    </a></li>
                    <li><a href="pages/contact.php" class="text-gray-300 hover:text-blue-400 transition flex items-center">
                        <i class="fas fa-envelope mr-2"></i>Contact
                    </a></li>
                    <li><a href="pages/privacy.php" class="text-gray-300 hover:text-blue-400 transition flex items-center">
                        <i class="fas fa-shield-alt mr-2"></i>Privacy Policy
                    </a></li>
                </ul>
            </div>
            
            <!-- Categories -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Categories</h4>
                <ul class="space-y-2">
                    <li><a href="<?php echo BASE_URL; ?>?category=1" class="text-gray-300 hover:text-blue-400 transition flex items-center">
                        <i class="fas fa-gem mr-2"></i>Jewelry
                    </a></li>
                    <li><a href="<?php echo BASE_URL; ?>?category=2" class="text-gray-300 hover:text-blue-400 transition flex items-center">
                        <i class="fas fa-laptop mr-2"></i>Electronics
                    </a></li>
                    <li><a href="<?php echo BASE_URL; ?>?category=3" class="text-gray-300 hover:text-blue-400 transition flex items-center">
                        <i class="fas fa-tshirt mr-2"></i>Fashion
                    </a></li>
                    <li><a href="<?php echo BASE_URL; ?>?category=4" class="text-gray-300 hover:text-blue-400 transition flex items-center">
                        <i class="fas fa-home mr-2"></i>Home & Garden
                    </a></li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Contact Info</h4>
                <div class="space-y-3">
                    <div class="flex items-center text-gray-300">
                        <i class="fas fa-envelope text-blue-400 mr-3"></i>
                        <span>info@ecommerce.com</span>
                    </div>
                    <div class="flex items-center text-gray-300">
                        <i class="fas fa-phone text-blue-400 mr-3"></i>
                        <span>+1 (555) 123-4567</span>
                    </div>
                    <div class="flex items-start text-gray-300">
                        <i class="fas fa-map-marker-alt text-blue-400 mr-3 mt-1"></i>
                        <span>123 Business St, City, State 12345</span>
                    </div>
                    <div class="flex items-center text-gray-300">
                        <i class="fas fa-clock text-blue-400 mr-3"></i>
                        <span>24/7 Customer Support</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Newsletter -->
        <div class="border-t border-gray-800 mt-8 pt-8">
            <div class="text-center mb-6">
                <h4 class="text-lg font-semibold mb-2">Subscribe to Our Newsletter</h4>
                <p class="text-gray-300 mb-4">Get the latest updates on new products and exclusive offers</p>
                <div class="flex flex-col sm:flex-row justify-center items-center gap-4 max-w-md mx-auto">
                    <input type="email" placeholder="Enter your email" 
                           class="flex-1 px-4 py-2 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-paper-plane mr-2"></i>Subscribe
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="border-t border-gray-800 pt-6 text-center">
            <p class="text-gray-400">
                &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved. 
                <span class="mx-2">|</span>
                <a href="pages/terms.php" class="hover:text-blue-400 transition">Terms of Service</a>
                <span class="mx-2">|</span>
                <a href="pages/privacy.php" class="hover:text-blue-400 transition">Privacy Policy</a>
            </p>
        </div>
    </div>
</footer>