-- Drop existing shipping tables and recreate with proper data
DROP TABLE IF EXISTS shipping_rates;
DROP TABLE IF EXISTS shipping_tracking;
DROP TABLE IF EXISTS shipping_zones;
DROP TABLE IF EXISTS shipping_methods;

-- Create shipping methods table
CREATE TABLE shipping_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    base_rate DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    per_kg_rate DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    delivery_time VARCHAR(50),
    carrier VARCHAR(100),
    active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create shipping zones table
CREATE TABLE shipping_zones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    pincode_pattern VARCHAR(20),
    base_rate DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create shipping rates table
CREATE TABLE shipping_rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    method_id INT NOT NULL,
    zone_id INT NOT NULL,
    min_weight DECIMAL(8,2) DEFAULT 0.00,
    max_weight DECIMAL(8,2) DEFAULT 999.99,
    rate DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (method_id) REFERENCES shipping_methods(id) ON DELETE CASCADE,
    FOREIGN KEY (zone_id) REFERENCES shipping_zones(id) ON DELETE CASCADE
);

-- Create shipping tracking table
CREATE TABLE shipping_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    tracking_number VARCHAR(100),
    carrier VARCHAR(100),
    status VARCHAR(50) DEFAULT 'pending',
    estimated_delivery DATE,
    actual_delivery DATE,
    tracking_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Insert shipping methods
INSERT INTO shipping_methods (name, description, base_rate, per_kg_rate, delivery_time, carrier, sort_order) VALUES
('Standard Delivery', '5-7 business days delivery', 50.00, 2.00, '5-7 days', 'India Post', 1),
('Express Delivery', '2-3 business days delivery', 75.00, 3.00, '2-3 days', 'BlueDart', 2),
('Same Day Delivery', 'Same day delivery in metro cities', 150.00, 5.00, 'Same day', 'Dunzo', 3);

-- Insert shipping zones
INSERT INTO shipping_zones (name, description, pincode_pattern, base_rate) VALUES
('Metro Cities', 'Major metropolitan cities', '110001,400001,700001,600001,560001,500001', 50.00),
('Tier 2 Cities', 'Secondary cities and towns', '1%,2%,3%,4%', 75.00),
('Remote Areas', 'Rural and remote locations', '%', 100.00);

-- Insert shipping rates
INSERT INTO shipping_rates (method_id, zone_id, min_weight, max_weight, rate) VALUES
(1, 1, 0.00, 5.00, 50.00),
(1, 2, 0.00, 5.00, 75.00),
(1, 3, 0.00, 5.00, 100.00),
(2, 1, 0.00, 5.00, 75.00),
(2, 2, 0.00, 5.00, 100.00),
(2, 3, 0.00, 5.00, 150.00),
(3, 1, 0.00, 2.00, 150.00);

-- Add shipping columns to orders table (only if not exists)
ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS shipping_method VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS shipping_cost DECIMAL(10,2) DEFAULT 0.00,
ADD COLUMN IF NOT EXISTS delivery_estimate VARCHAR(100) NULL;