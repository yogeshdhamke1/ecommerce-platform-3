# E-Commerce Platform 🛒

A complete PHP-based e-commerce platform with multi-currency support, modern UI/UX design, and comprehensive business management features.

## 🚀 Live Demo
- **Homepage**: Browse products with INR pricing
- **Admin Panel**: `/admin/index.php`
- **Customer Dashboard**: Login required

## 🔑 Test Credentials

### Admin Account
- **Email**: `admin@test.com`
- **Password**: `password`
- **Access**: Full admin panel

### Customer Accounts
- **Email**: `test@test.com` | **Password**: `password`
- **Email**: `customer@test.com` | **Password**: `password`

## ✨ Features

### 🛍️ E-Commerce Core
- **25 Products** across 5 categories (Jewelry, Electronics, Fashion, Home & Garden, Sports)
- **Advanced Search & Filtering** with real-time results
- **Shopping Cart** with quantity controls and AJAX updates
- **Secure Checkout** with order tracking
- **Order Management** with status updates

### 💱 Multi-Currency Support
- **Default**: Indian Rupee (INR ₹)
- **Supported**: USD, EUR, GBP, JPY
- **Real-time Conversion** with persistent selection
- **Professional Formatting** with proper symbols

### 👥 Customer Management
- **User Authentication** (Login/Register/Password Reset)
- **Customer Dashboard** with statistics
- **Profile Management** with editable information
- **Order History** with detailed tracking
- **Wishlist** and **Reviews** system

### 📄 Invoice System
- **PDF Generation** with multi-currency support
- **Professional Layout** with order details
- **Download/Email** functionality

### 🔧 Admin Panel
- **Dashboard** with sales statistics
- **Product Management** (CRUD operations)
- **Order Management** with status updates
- **User Management** and analytics

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+, MySQL 8.0+
- **Frontend**: Tailwind CSS 3.0, Font Awesome 6.5.1
- **Database**: MySQL with PDO
- **Dependencies**: Composer (DomPDF)

## 📦 Installation

### Prerequisites
- PHP 7.4+ with MySQL extension
- MySQL 8.0+
- Composer
- Web server (Apache/Nginx)

### Quick Setup

1. **Clone Repository**
```bash
git clone https://github.com/yogeshdhamke1/ecommerce-platform-3.git
cd ecommerce-platform-3
```

2. **Database Setup**
```sql
CREATE DATABASE `ecommerce-store_db`;
mysql -u root -p ecommerce-store_db < database.sql
```

3. **Configure Database**
```php
// Edit config/database.php
private $host = 'localhost';
private $db_name = 'ecommerce-store_db';
private $username = 'root';
private $password = '';
```

4. **Install Dependencies**
```bash
composer install
```

5. **Access Application**
- Homepage: `http://localhost/ecommerce-platform-3/`
- Admin: `http://localhost/ecommerce-platform-3/admin/`

## 🎨 UI/UX Features

- **Modern Design** with Tailwind CSS
- **Responsive Layout** for all devices
- **Interactive Elements** with hover effects
- **Loading States** and notifications
- **Mobile-First** approach

## 🔒 Security Features

- **Password Hashing** with PHP password_hash()
- **SQL Injection Prevention** with prepared statements
- **XSS Protection** with input sanitization
- **Session Security** management

## 📱 Mobile Optimization

- **Touch-Friendly** interface
- **Responsive Grid** system
- **Optimized Images** with fallbacks
- **Fast Loading** performance

## 🔍 SEO Friendly

- **Meta Tags** optimization
- **Semantic HTML5** structure
- **Clean URLs** and navigation
- **Image Alt Tags** for accessibility

## 📊 Sample Data

### Products (INR Pricing)
- Diamond Ring: ₹1,08,000
- Smartphone: ₹66,500
- Laptop: ₹1,08,000
- Designer Dress: ₹16,600

### Categories
- Jewelry, Electronics, Fashion, Home & Garden, Sports

## 🗂️ Project Structure

```
ecommerce-platform-3/
├── config/              # Database and app configuration
├── classes/             # PHP business logic
├── pages/               # Customer pages
├── admin/               # Admin panel
├── assets/              # CSS, JS, images
├── includes/            # Reusable components
├── documentation/       # Project docs
├── database.sql         # Database schema
└── update-database-query.sql # Update queries
```

## 🧪 Testing

1. **User Registration**: Create account or use test credentials
2. **Shopping Flow**: Browse → Add to Cart → Checkout
3. **Currency Switch**: Test INR, USD, EUR, GBP, JPY
4. **Admin Panel**: Manage products and orders
5. **Mobile Testing**: Responsive design verification

## 📚 Documentation

- **[Project Structure](documentation/project-structure.md)** - Complete file organization
- **[Features](documentation/features.md)** - Detailed feature breakdown
- **[Installation](documentation/installation.md)** - Setup guide
- **[API Endpoints](documentation/api-endpoints.md)** - AJAX documentation

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 👨‍💻 Developer

**Yogesh Dhamke**
- GitHub: [@yogeshdhamke1](https://github.com/yogeshdhamke1)
- Repository: [ecommerce-platform-3](https://github.com/yogeshdhamke1/ecommerce-platform-3)

## 🆘 Support

For support and questions:
- Check [Documentation](documentation/)
- Review [Installation Guide](documentation/installation.md)
- Open an [Issue](https://github.com/yogeshdhamke1/ecommerce-platform-3/issues)

---

⭐ **Star this repository if you find it helpful!**