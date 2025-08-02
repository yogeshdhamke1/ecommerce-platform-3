-- Update Database Query for E-Commerce Platform
-- Run these queries to update existing database with new test credentials and INR pricing

-- Add reset token columns if not exists
ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_token VARCHAR(64) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_expires DATETIME NULL;

-- Create wishlist table if not exists
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Clear existing users
DELETE FROM users;

-- Insert updated test users with new credentials
INSERT INTO users (username, email, password, full_name, is_admin) VALUES
('admin', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', TRUE),
('testuser', 'test@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User', FALSE),
('customer1', 'customer@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Customer One', FALSE);

-- Update product prices to Indian Rupees (INR)
UPDATE products SET price = 108000.00 WHERE name = 'Diamond Ring';
UPDATE products SET price = 75000.00 WHERE name = 'Gold Necklace';
UPDATE products SET price = 16500.00 WHERE name = 'Silver Bracelet';
UPDATE products SET price = 25000.00 WHERE name = 'Pearl Earrings';
UPDATE products SET price = 50000.00 WHERE name = 'Ruby Pendant';

UPDATE products SET price = 66500.00 WHERE name = 'Smartphone';
UPDATE products SET price = 108000.00 WHERE name = 'Laptop';
UPDATE products SET price = 25000.00 WHERE name = 'Headphones';
UPDATE products SET price = 41500.00 WHERE name = 'Tablet';
UPDATE products SET price = 33200.00 WHERE name = 'Smart Watch';

UPDATE products SET price = 16600.00 WHERE name = 'Designer Dress';
UPDATE products SET price = 25000.00 WHERE name = 'Leather Jacket';
UPDATE products SET price = 10800.00 WHERE name = 'Running Shoes';
UPDATE products SET price = 12500.00 WHERE name = 'Handbag';
UPDATE products SET price = 7500.00 WHERE name = 'Sunglasses';

UPDATE products SET price = 33200.00 WHERE name = 'Coffee Table';
UPDATE products SET price = 12500.00 WHERE name = 'Garden Chair';
UPDATE products SET price = 4150.00 WHERE name = 'Flower Vase';
UPDATE products SET price = 16600.00 WHERE name = 'Wall Art';
UPDATE products SET price = 6650.00 WHERE name = 'Plant Pot';

UPDATE products SET price = 16600.00 WHERE name = 'Tennis Racket';
UPDATE products SET price = 4150.00 WHERE name = 'Basketball';
UPDATE products SET price = 3320.00 WHERE name = 'Yoga Mat';
UPDATE products SET price = 25000.00 WHERE name = 'Dumbbells';
UPDATE products SET price = 49900.00 WHERE name = 'Bicycle';

-- Update existing orders currency to INR (if any)
UPDATE orders SET currency = 'INR' WHERE currency = 'USD';

-- Clear cart and wishlist for fresh testing
DELETE FROM cart;
DELETE FROM wishlist;
DELETE FROM reviews;

-- Reset auto increment for clean IDs
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE orders AUTO_INCREMENT = 1;
ALTER TABLE cart AUTO_INCREMENT = 1;
ALTER TABLE wishlist AUTO_INCREMENT = 1;
ALTER TABLE reviews AUTO_INCREMENT = 1;

-- Verify updates
SELECT 'Users Updated:' as Status, COUNT(*) as Count FROM users;
SELECT 'Products with INR Pricing:' as Status, COUNT(*) as Count FROM products WHERE price > 1000;
SELECT 'Wishlist Table Ready:' as Status, 'YES' as Count FROM information_schema.tables WHERE table_name = 'wishlist';
SELECT 'Sample Product Prices:' as Status;
SELECT name, CONCAT('₹', FORMAT(price, 2)) as Price FROM products LIMIT 5;