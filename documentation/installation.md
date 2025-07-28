# E-Commerce Platform - Installation Guide

## 📋 System Requirements

### **Server Requirements**
- **PHP 7.4+** with extensions:
  - PDO MySQL
  - JSON
  - Session
  - Hash
- **MySQL 8.0+** database server
- **Apache/Nginx** web server
- **Composer** for dependency management

### **Development Environment**
- **XAMPP/WAMP/MAMP** for local development
- **Modern Browser** (Chrome, Firefox, Safari, Edge)
- **Text Editor/IDE** (VS Code, PhpStorm, Sublime)

## 🚀 Installation Steps

### **1. Download and Setup**
```bash
# Clone or download the project
# Extract to your web server directory
# Example: C:\xampp\htdocs\Yogesh\0\E-Commerce Platform\
```

### **2. Database Setup**
```sql
-- Create database
CREATE DATABASE `ecommerce-store_db`;

-- Import database schema
mysql -u root -p ecommerce-store_db < database.sql
```

### **3. Configure Database Connection**
Edit `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'ecommerce-store_db';
private $username = 'root';        // Your MySQL username
private $password = '';            // Your MySQL password
```

### **4. Install Dependencies**
```bash
# Navigate to project directory
cd "E-Commerce Platform"

# Install Composer dependencies
composer install
```

### **5. Configure Application**
Edit `config/config.php`:
```php
// Update base URL to match your setup
define('BASE_URL', 'http://localhost/Yogesh/0/E-Commerce Platform/');

// Update site name if needed
define('SITE_NAME', 'Your Store Name');
```

### **6. Set Permissions**
```bash
# Ensure proper file permissions
chmod 755 assets/images/
chmod 644 *.php
```

## 🔧 Configuration Options

### **Currency Settings**
Edit currency rates in `config/config.php`:
```php
$currencies = [
    'USD' => ['symbol' => '$', 'rate' => 1.00],
    'EUR' => ['symbol' => '€', 'rate' => 0.85],
    'GBP' => ['symbol' => '£', 'rate' => 0.73],
    'INR' => ['symbol' => '₹', 'rate' => 83.12],
    'JPY' => ['symbol' => '¥', 'rate' => 149.50]
];
```

### **Email Configuration** (Optional)
For password reset functionality, configure email settings:
```php
// Add to config/config.php
define('SMTP_HOST', 'your-smtp-host');
define('SMTP_USERNAME', 'your-email@domain.com');
define('SMTP_PASSWORD', 'your-email-password');
```

## 👤 Default Admin Account

### **Admin Login Credentials**
- **Email**: `admin@ecommerce.com`
- **Password**: `password`
- **Access**: `/admin/index.php`

### **Test Accounts Created**
- **Admin**: `admin@test.com` / `password`
- **Customer 1**: `test@test.com` / `password`  
- **Customer 2**: `customer@test.com` / `password`

### **Change Passwords**
```sql
-- Update password in database
UPDATE users SET password = '$2y$10$newhashedpassword' WHERE email = 'admin@test.com';
```

## 🗂️ File Structure Setup

### **Required Directories**
```
E-Commerce Platform/
├── assets/images/          # Product images (writable)
├── vendor/                 # Composer dependencies
├── documentation/          # Project documentation
└── admin/                  # Admin panel files
```

### **Image Upload Directory**
```bash
# Create and set permissions for image uploads
mkdir assets/images/uploads
chmod 755 assets/images/uploads
```

## 🔍 Testing Installation

### **1. Access Homepage**
Visit: `http://localhost/Yogesh/0/E-Commerce Platform/`

### **2. Test User Registration**
- Go to registration page
- Create a test account
- Verify login functionality

### **3. Test Admin Panel**
- Login with admin credentials
- Access: `http://localhost/Yogesh/0/E-Commerce Platform/admin/`
- Verify dashboard loads correctly

### **4. Test Core Features**
- Browse products
- Add items to cart
- Test currency switching
- Place a test order

## 🐛 Troubleshooting

### **Common Issues**

#### **Database Connection Error**
```
Solution: Check database credentials in config/database.php
Verify MySQL service is running
Ensure database exists and is accessible
```

#### **Missing Dependencies**
```
Error: Class 'Dompdf\Dompdf' not found
Solution: Run 'composer install' in project directory
```

#### **Permission Errors**
```
Solution: Set proper file permissions
chmod 755 for directories
chmod 644 for PHP files
```

#### **Images Not Loading**
```
Solution: Check assets/images/ directory exists
Verify image file permissions
Ensure demo-product.jpg fallback exists
```

### **Debug Mode**
Enable error reporting for development:
```php
// Add to config/config.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## 🔒 Security Considerations

### **Production Setup**
- Change default admin password
- Update database credentials
- Disable error reporting
- Enable HTTPS
- Set secure session settings
- Configure proper file permissions

### **Environment Variables**
Consider using environment variables for sensitive data:
```php
// Example: Use $_ENV for database credentials
$username = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';
```

## 📊 Performance Optimization

### **Database Optimization**
```sql
-- Add indexes for better performance
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_cart_user ON cart(user_id);
```

### **Caching** (Optional)
- Enable PHP OPcache
- Use Redis/Memcached for sessions
- Implement query result caching

## 🚀 Deployment

### **Production Checklist**
- [ ] Update BASE_URL in config
- [ ] Change admin password
- [ ] Disable debug mode
- [ ] Set secure file permissions
- [ ] Configure SSL certificate
- [ ] Set up automated backups
- [ ] Configure email settings
- [ ] Test all functionality

### **Server Configuration**
```apache
# .htaccess for Apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

## 📞 Support

For installation issues or questions:
- Check troubleshooting section
- Review error logs
- Verify system requirements
- Test with default configuration