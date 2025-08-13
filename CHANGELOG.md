# Changelog

All notable changes to this project will be documented in this file.

## [2.0.0] - 2024-12-19

### 🆕 Added
- **OTP Authentication System**
  - Email and mobile OTP login options
  - Integrated OTP login in main login page
  - 6-digit OTP with 10-minute expiry
  - Database table for OTP storage

- **AI Recommendation Engine**
  - Smart product recommendations based on user behavior
  - Personalized product suggestions on product pages
  - Dedicated recommendations page
  - Fallback to popular products for new users



- **Enhanced Admin Panel**
  - Advanced product filtering (search, category, stock status)
  - Image upload and URL options for products
  - Real-time filtering without page refresh
  - Improved product management interface

- **PDF Invoice System**
  - Downloadable HTML invoices
  - Professional invoice layout
  - Customer and order details
  - Print-friendly design

- **Dashboard Enhancements**
  - Wishlist items count display
  - Reviews count display
  - Real-time statistics
  - Improved user experience

### 🔧 Improved
- **Currency System**
  - Moved currency selector from header to footer
  - Better user experience with horizontal layout
  - Cleaner header navigation

- **Product Management**
  - Fixed image update issues in admin panel
  - Proper database image updates
  - Support for both file upload and URL input
  - Fallback to demo image for missing images

- **Recommendation Algorithm**
  - Simplified algorithm for better performance
  - Always shows products (no empty states)
  - Random product selection for variety
  - Stock-based filtering

### 🐛 Fixed
- **Image Upload Issues**
  - Fixed product image updates in database
  - Proper handling of existing images during edits
  - Correct image display on website

- **PDF Generation**
  - Fixed "Failed to load PDF document" error
  - Implemented working HTML-based invoice download
  - Proper content visibility in downloaded files

- **Recommendation Display**
  - Fixed empty recommendations page
  - Ensured products always display
  - Proper fallback mechanisms

### 🗄️ Database Changes
- Added `user_otps` table for OTP authentication
- Enhanced product management with image handling
- Optimized recommendation queries

### 📁 New Files Added
- `pages/otp_login.php` - OTP authentication page
- `create_otp_table.php` - Database setup script
- `admin/add_categories.php` - Category setup script
- `website-builder/` - Complete website builder application
- `DOCUMENTATION.md` - Comprehensive project documentation
- `CHANGELOG.md` - This changelog file

### 🔄 Modified Files
- `pages/login.php` - Integrated OTP login options
- `pages/dashboard.php` - Added wishlist and reviews count
- `pages/recommendations.php` - Enhanced recommendation display
- `pages/product.php` - Added recommendation slider
- `pages/invoice.php` - Fixed PDF generation
- `admin/products.php` - Enhanced filtering and image handling
- `classes/Product.php` - Improved image update handling
- `classes/Recommendations.php` - Simplified algorithm
- `includes/header.php` - Removed currency selector
- `includes/footer.php` - Added currency selector
- `README.md` - Updated with new features

## [1.0.0] - 2024-12-18

### 🎉 Initial Release
- Basic e-commerce functionality
- User authentication system
- Product catalog and management
- Shopping cart and checkout
- Order management
- Admin panel
- Multi-currency support
- Social media integration
- Shipping calculator
- Product image zoom
- Search and filtering

---

**Note:** Version 2.0.0 represents a major enhancement with advanced authentication, AI recommendations, and bonus website builder functionality.