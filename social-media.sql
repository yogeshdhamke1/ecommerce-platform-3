-- Social Media Integration Database Updates

-- Add social login columns to users table
ALTER TABLE users 
ADD COLUMN social_provider VARCHAR(50) NULL,
ADD COLUMN social_id VARCHAR(255) NULL,
ADD INDEX idx_social_provider (social_provider),
ADD INDEX idx_social_id (social_id);

-- Create social shares tracking table
CREATE TABLE IF NOT EXISTS social_shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NULL,
    platform VARCHAR(50) NOT NULL,
    shared_url TEXT NOT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_product_shares (product_id),
    INDEX idx_platform (platform),
    INDEX idx_created (created_at)
);

-- Create social login attempts table for security
CREATE TABLE IF NOT EXISTS social_login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider VARCHAR(50) NOT NULL,
    social_id VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    success BOOLEAN DEFAULT FALSE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_provider (provider),
    INDEX idx_social_id (social_id),
    INDEX idx_created (created_at)
);