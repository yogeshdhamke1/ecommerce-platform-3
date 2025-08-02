-- Advanced Search and Filters Database Updates
-- Add indexes for better search performance

-- Add indexes for faster searching
CREATE INDEX IF NOT EXISTS idx_products_name ON products(name);
CREATE INDEX IF NOT EXISTS idx_products_price ON products(price);
CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_products_stock ON products(stock);
CREATE INDEX IF NOT EXISTS idx_products_created ON products(created_at);

-- Add indexes for reviews table for rating-based filtering
CREATE INDEX IF NOT EXISTS idx_reviews_product ON reviews(product_id);
CREATE INDEX IF NOT EXISTS idx_reviews_rating ON reviews(rating);

-- Add full-text search capability (MySQL 5.6+)
ALTER TABLE products ADD FULLTEXT(name, description);

-- Create search history table for analytics (optional)
CREATE TABLE IF NOT EXISTS search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    search_query VARCHAR(255) NOT NULL,
    results_count INT DEFAULT 0,
    filters_used JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create product views table for popularity tracking
CREATE TABLE IF NOT EXISTS product_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Add indexes for product views
CREATE INDEX IF NOT EXISTS idx_product_views_product ON product_views(product_id);
CREATE INDEX IF NOT EXISTS idx_product_views_created ON product_views(created_at);

-- Insert sample data for testing advanced search
INSERT IGNORE INTO products (name, description, price, category_id, image, stock) VALUES
('Premium Diamond Ring', 'Elegant diamond ring with 18k gold setting, perfect for special occasions', 25000.00, 1, 'demo-product.jpg', 5),
('Wireless Bluetooth Headphones', 'High-quality wireless headphones with noise cancellation and 30-hour battery life', 8999.00, 2, 'demo-product.jpg', 25),
('Designer Cotton T-Shirt', 'Premium cotton t-shirt with modern design, available in multiple colors', 1299.00, 3, 'demo-product.jpg', 50),
('Smart Home Security Camera', 'WiFi-enabled security camera with night vision and mobile app control', 4999.00, 4, 'demo-product.jpg', 15),
('Professional Tennis Racket', 'Lightweight carbon fiber tennis racket for professional players', 12999.00, 5, 'demo-product.jpg', 8),
('Luxury Watch Collection', 'Swiss-made luxury watch with automatic movement and sapphire crystal', 45000.00, 1, 'demo-product.jpg', 3),
('Gaming Laptop', 'High-performance gaming laptop with RTX graphics and 16GB RAM', 89999.00, 2, 'demo-product.jpg', 12),
('Casual Denim Jeans', 'Comfortable slim-fit denim jeans with stretch fabric', 2499.00, 3, 'demo-product.jpg', 30),
('Indoor Plant Collection', 'Set of 3 air-purifying indoor plants with decorative pots', 1899.00, 4, 'demo-product.jpg', 20),
('Yoga Mat Premium', 'Non-slip premium yoga mat with alignment guides and carrying strap', 2999.00, 5, 'demo-product.jpg', 40);

-- Insert sample reviews for rating-based filtering
INSERT IGNORE INTO reviews (product_id, user_id, rating, comment) VALUES
(1, 1, 5, 'Absolutely beautiful ring, excellent quality!'),
(1, 2, 4, 'Great product, fast delivery'),
(2, 1, 5, 'Amazing sound quality and battery life'),
(2, 3, 4, 'Good headphones, comfortable to wear'),
(3, 2, 3, 'Nice t-shirt but sizing runs small'),
(4, 1, 5, 'Perfect for home security, easy setup'),
(5, 3, 4, 'Great racket for intermediate players'),
(6, 2, 5, 'Luxury watch worth every penny'),
(7, 1, 4, 'Excellent gaming performance'),
(8, 3, 4, 'Comfortable jeans, good fit'),
(9, 2, 5, 'Beautiful plants, arrived in perfect condition'),
(10, 1, 4, 'Good quality yoga mat, non-slip surface');