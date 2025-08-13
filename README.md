# Modern E-Commerce Platform 🛒

A comprehensive PHP-based e-commerce platform with advanced features including OTP authentication, AI recommendations, social media integration, and multi-currency support.

## 🚀 Live Demo
- **Homepage**: Browse products with multi-currency support
- **Admin Panel**: `/admin/index.php`
- **Customer Dashboard**: Login required

## 🔑 Test Credentials

### Admin Account
- **Email**: `admin@test.com`
- **Password**: `password`
- **Access**: Full admin panel with shipping management

### Customer Accounts
- **Email**: `test@test.com` | **Password**: `password`
- **Email**: `customer@test.com` | **Password**: `password`

## ✨ Advanced Features

### 🛍️ E-Commerce Core
- **25+ Products** across 5 categories (Jewelry, Electronics, Fashion, Home & Garden, Sports)
- **Advanced Search & Filtering** with real-time autocomplete
- **Smart Product Recommendations** based on user behavior
- **Shopping Cart** with quantity controls and AJAX updates
- **Secure Checkout** with multiple shipping options
- **Order Management** with tracking and invoices

### 🔍 Advanced Search System
- **Real-time Autocomplete** with product suggestions
- **Multi-criteria Filtering** (price range, rating, category, stock)
- **Sorting Options** (price, rating, popularity, name, newest)
- **Mobile-responsive** search interface
- **Search Analytics** and history tracking

### 📦 Shipping & Logistics
- **Real-time Rate Calculation** based on location and weight
- **Multiple Shipping Methods** (Standard, Express, Same Day)
- **Zone-based Pricing** (Metro, Tier 2, Remote areas)
- **Carrier Integration** (India Post, BlueDart, Dunzo)
- **Delivery Time Estimation** with date ranges
- **Pincode Validation** for Indian addresses
- **Admin Shipping Management** panel

### 🔗 Social Media Integration
- **Social Login** (Google OAuth, Facebook Login)
- **Product Sharing** across 6 platforms (Facebook, Twitter, WhatsApp, LinkedIn, Pinterest, Telegram)
- **Copy-to-clipboard** functionality
- **Social Share Tracking** for analytics
- **Viral Marketing** features

### 🖼️ Product Image Zoom
- **Desktop Magnifier Lens** with real-time tracking
- **Mobile Pinch-to-zoom** and double-tap support
- **Fullscreen Modal** with zoom controls
- **Touch-friendly** interface for all devices
- **Smooth Animations** and transitions

### 🤖 Smart Recommendations
- **AI-powered Suggestions** based on purchase history
- **Category Preference Learning** for personalized experience
- **Popular Products** fallback for new users
- **Related Products** display on product pages
- **Cross-selling** opportunities

### 💱 Multi-Currency Support
- **Default**: Indian Rupee (INR ₹)
- **Supported**: USD, EUR, GBP, JPY
- **Real-time Conversion** with exchange rates
- **Persistent Selection** across sessions
- **Professional Formatting** with proper symbols

### 👥 Customer Management
- **User Authentication** with social login options
- **Customer Dashboard** with personalized statistics
- **Profile Management** with editable information
- **Order History** with detailed tracking
- **Wishlist** and **Reviews** system
- **Invoice Generation** (PDF download)

### 🔧 Admin Panel
- **Comprehensive Dashboard** with analytics
- **Product Management** (CRUD operations)
- **Shipping Configuration** (methods, zones, rates)
- **Order Management** with shipping details
- **User Management** and social login tracking
- **Coupon Management** system
- **Sales Reports** and analytics

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+, MySQL 8.0+
- **Frontend**: Tailwind CSS 3.0, Font Awesome 6.5.1, JavaScript ES6+
- **Database**: MySQL with PDO and prepared statements
- **APIs**: Google OAuth 2.0, Facebook Graph API
- **PDF Generation**: Custom TCPDF implementation
- **Security**: Password hashing, XSS protection, SQL injection prevention

## 📦 Installation

### Prerequisites
- PHP 7.4+ with MySQL, cURL extensions
- MySQL 8.0+
- Web server (Apache/Nginx)
- Google & Facebook developer accounts (for social login)

### Quick Setup

1. **Clone Repository**
```bash
git clone <repository-url>
cd E-Commerce-Platform
```

2. **Database Setup**
```sql
CREATE DATABASE `ecommerce-store_db`;
mysql -u root -p ecommerce-store_db < database.sql
mysql -u root -p ecommerce-store_db < additional-features.sql
mysql -u root -p ecommerce-store_db < shipping-final.sql
mysql -u root -p ecommerce-store_db < social-media.sql
```

3. **Configure Database**
```php
// Edit config/database.php
private $host = 'localhost';
private $db_name = 'ecommerce-store_db';
private $username = 'root';
private $password = '';
```

4. **Configure Social Login**
```php
// Edit config/social_config.php
define('GOOGLE_CLIENT_ID', 'your-google-client-id');
define('FACEBOOK_APP_ID', 'your-facebook-app-id');
```

5. **Access Application**
- Homepage: `http://localhost/E-Commerce-Platform/`
- Admin: `http://localhost/E-Commerce-Platform/admin/`

## 🎨 UI/UX Features

- **Modern Design** with Tailwind CSS
- **Responsive Layout** for all devices
- **Interactive Elements** with hover effects and animations
- **Loading States** and real-time notifications
- **Mobile-First** approach with touch optimization
- **Professional Checkout** flow with progress indicators

## 🔒 Security Features

- **Password Hashing** with PHP password_hash()
- **SQL Injection Prevention** with prepared statements
- **XSS Protection** with input sanitization
- **CSRF Protection** for forms
- **Session Security** management
- **OAuth Security** for social logins

## 📱 Mobile Optimization

- **Touch-Friendly** interface with gesture support
- **Responsive Grid** system with breakpoints
- **Optimized Images** with lazy loading
- **Fast Loading** performance optimization
- **Mobile Search** with autocomplete
- **Touch Zoom** for product images

## 🔍 SEO & Analytics

- **Meta Tags** optimization for products
- **Semantic HTML5** structure
- **Clean URLs** and breadcrumb navigation
- **Image Alt Tags** for accessibility
- **Search Analytics** tracking
- **Social Share** metrics

## 📊 Sample Data

### Products (INR Pricing)
- Premium Diamond Ring: ₹25,000
- Wireless Headphones: ₹8,999
- Gaming Laptop: ₹89,999
- Designer T-Shirt: ₹1,299
- Smart Security Camera: ₹4,999

### Shipping Methods
- Standard Delivery: ₹50 base + ₹2/kg (5-7 days)
- Express Delivery: ₹75 base + ₹3/kg (2-3 days)
- Same Day Delivery: ₹150 base + ₹5/kg (Metro only)

## 🗂️ Project Structure

```
E-Commerce-Platform/
├── config/              # Database and social media configuration
├── classes/             # Business logic (Product, User, Cart, Shipping, etc.)
├── pages/               # Customer pages with advanced features
├── admin/               # Admin panel with shipping management
├── assets/              # CSS, JS, images, and zoom functionality
├── includes/            # Reusable components and widgets
├── documentation/       # Project documentation
├── database.sql         # Core database schema
├── shipping-final.sql   # Shipping system tables
├── social-media.sql     # Social integration tables
└── additional-features.sql # Extended features
```

## 🧪 Testing Features

1. **Advanced Search**: Test autocomplete and filtering
2. **Social Login**: Try Google/Facebook authentication
3. **Shipping Calculator**: Enter different pincodes
4. **Product Zoom**: Test on desktop and mobile
5. **Social Sharing**: Share products on social media
6. **Recommendations**: Browse products to see suggestions
7. **Admin Shipping**: Configure methods and rates
8. **Multi-currency**: Switch between currencies

## 📚 Key Components

### Search System
- `classes/Product.php` - Advanced search logic
- `pages/search.php` - Dedicated search page
- `pages/search_api.php` - Autocomplete API
- `includes/search_widget.php` - Reusable component

### Shipping System
- `classes/Shipping.php` - Rate calculation engine
- `pages/shipping_calculator.php` - API endpoint
- `admin/shipping.php` - Admin management
- `includes/shipping_widget.php` - Calculator widget

### Social Integration
- `config/social_config.php` - OAuth configuration
- `pages/social_login.php` - Authentication handler
- `includes/social_buttons.php` - Login and share buttons

### Image Zoom
- `assets/css/zoom.css` - Zoom styling
- `assets/js/mobile-zoom.js` - Touch support
- `includes/image_zoom.php` - Reusable component

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🆘 Support

For support and questions:
- Check [Documentation](documentation/)
- Review feature-specific SQL files
- Open an Issue for bugs or feature requests

---

⭐ **Star this repository if you find it helpful!**

**Built with ❤️ using PHP, MySQL, and modern web technologies**