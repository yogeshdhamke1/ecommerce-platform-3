-- Additional Features Database Schema

-- Payments table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    transaction_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- Coupons table
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('percentage', 'fixed') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    minimum_amount DECIMAL(10,2) DEFAULT 0,
    max_discount DECIMAL(10,2) NULL,
    usage_limit INT NULL,
    usage_count INT DEFAULT 0,
    expires_at DATETIME NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Notifications table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Loyalty points table
CREATE TABLE loyalty_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    points INT NOT NULL,
    type ENUM('earned', 'redeemed') NOT NULL,
    description VARCHAR(200),
    order_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- Product alerts table
CREATE TABLE product_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    email VARCHAR(100) NOT NULL,
    is_notified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Abandoned carts table
CREATE TABLE abandoned_carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    email_sent BOOLEAN DEFAULT FALSE,
    last_email_sent TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Shipping rates table
CREATE TABLE shipping_rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    region VARCHAR(100) NOT NULL,
    min_weight DECIMAL(8,2) DEFAULT 0,
    max_weight DECIMAL(8,2) DEFAULT 999999,
    rate DECIMAL(10,2) NOT NULL,
    free_shipping_threshold DECIMAL(10,2) NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Customer feedback table
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_id INT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    feedback_text TEXT,
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- Insert sample data
INSERT INTO coupons (code, type, value, minimum_amount, max_discount, usage_limit) VALUES
('WELCOME10', 'percentage', 10.00, 1000.00, 500.00, 100),
('SAVE500', 'fixed', 500.00, 2000.00, NULL, 50),
('FIRST20', 'percentage', 20.00, 1500.00, 1000.00, 200);

INSERT INTO shipping_rates (region, min_weight, max_weight, rate, free_shipping_threshold) VALUES
('Local', 0, 5, 50.00, 1000.00),
('Regional', 0, 5, 100.00, 1500.00),
('National', 0, 5, 150.00, 2000.00);

-- Add loyalty points column to users table
ALTER TABLE users ADD COLUMN loyalty_points INT DEFAULT 0;