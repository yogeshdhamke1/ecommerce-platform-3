-- MySQL 8.0+ Database Schema for E-Commerce Platform

CREATE DATABASE `ecommerce-store_db`;
USE `ecommerce-store_db`;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Cart table
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10,2) NOT NULL,
    shipping_address TEXT NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Wishlist table
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Reviews table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Addresses table
CREATE TABLE addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type VARCHAR(20) DEFAULT 'shipping',
    full_name VARCHAR(100),
    address_line1 VARCHAR(255),
    address_line2 VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100),
    is_default BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Jewelry', 'Fine jewelry and accessories'),
('Electronics', 'Latest electronic gadgets and devices'),
('Fashion', 'Clothing and fashion accessories'),
('Home & Garden', 'Home decor and garden supplies'),
('Sports', 'Sports equipment and accessories');

-- Insert sample products
INSERT INTO products (name, description, price, category_id, image, stock) VALUES
('Diamond Ring', 'Beautiful diamond engagement ring', 108000.00, 1, 'diamond-ring.jpg', 10),
('Gold Necklace', 'Elegant gold necklace', 75000.00, 1, 'gold-necklace.jpg', 15),
('Silver Bracelet', 'Sterling silver bracelet', 16500.00, 1, 'silver-bracelet.jpg', 25),
('Pearl Earrings', 'Classic pearl earrings', 25000.00, 1, 'pearl-earrings.jpg', 20),
('Ruby Pendant', 'Stunning ruby pendant', 50000.00, 1, 'ruby-pendant.jpg', 12),

('Smartphone', 'Latest smartphone with advanced features', 66500.00, 2, 'smartphone.jpg', 50),
('Laptop', 'High-performance laptop', 108000.00, 2, 'laptop.jpg', 30),
('Headphones', 'Wireless noise-canceling headphones', 25000.00, 2, 'headphones.jpg', 40),
('Tablet', '10-inch tablet with HD display', 41500.00, 2, 'tablet.jpg', 35),
('Smart Watch', 'Fitness tracking smartwatch', 33200.00, 2, 'smartwatch.jpg', 45),

('Designer Dress', 'Elegant designer dress', 16600.00, 3, 'designer-dress.jpg', 20),
('Leather Jacket', 'Premium leather jacket', 25000.00, 3, 'leather-jacket.jpg', 15),
('Running Shoes', 'Comfortable running shoes', 10800.00, 3, 'running-shoes.jpg', 60),
('Handbag', 'Stylish leather handbag', 12500.00, 3, 'handbag.jpg', 25),
('Sunglasses', 'UV protection sunglasses', 7500.00, 3, 'sunglasses.jpg', 40),

('Coffee Table', 'Modern wooden coffee table', 33200.00, 4, 'coffee-table.jpg', 10),
('Garden Chair', 'Comfortable outdoor chair', 12500.00, 4, 'garden-chair.jpg', 30),
('Flower Vase', 'Decorative ceramic vase', 4150.00, 4, 'flower-vase.jpg', 50),
('Wall Art', 'Abstract wall art piece', 16600.00, 4, 'wall-art.jpg', 20),
('Plant Pot', 'Large ceramic plant pot', 6650.00, 4, 'plant-pot.jpg', 35),

('Tennis Racket', 'Professional tennis racket', 16600.00, 5, 'tennis-racket.jpg', 25),
('Basketball', 'Official size basketball', 4150.00, 5, 'basketball.jpg', 40),
('Yoga Mat', 'Non-slip yoga mat', 3320.00, 5, 'yoga-mat.jpg', 60),
('Dumbbells', 'Adjustable dumbbells set', 25000.00, 5, 'dumbbells.jpg', 20),
('Bicycle', 'Mountain bike', 49900.00, 5, 'bicycle.jpg', 15);

-- Add reset token columns to users table
ALTER TABLE users ADD COLUMN reset_token VARCHAR(64) NULL;
ALTER TABLE users ADD COLUMN reset_expires DATETIME NULL;

-- Create test users
INSERT INTO users (username, email, password, full_name, is_admin) VALUES
('admin', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', TRUE),
('testuser', 'test@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User', FALSE),
('customer1', 'customer@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Customer One', FALSE);