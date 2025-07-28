# E-Commerce Platform - Project Structure

## 📁 Root Directory
```
E-Commerce Platform/
├── index.php                 # Main homepage with product listing
├── database.sql             # MySQL database schema and sample data
├── composer.json            # PHP dependencies (DomPDF)
└── README.md               # Project overview and setup instructions
```

## 📁 config/
**Configuration files for database and application settings**
```
config/
├── config.php              # Main configuration, currency settings, session management
└── database.php            # Database connection class for MySQL
```

## 📁 classes/
**PHP classes for business logic and data management**
```
classes/
├── User.php                # User authentication, registration, profile management
├── Product.php             # Product CRUD operations, search, filtering
├── Cart.php                # Shopping cart management, add/remove/update items
├── Order.php               # Order creation, tracking, status management
└── Invoice.php             # PDF invoice generation using DomPDF
```

## 📁 pages/
**Customer-facing pages and functionality**
```
pages/
├── login.php               # User login with modern UI
├── register.php            # User registration form
├── forgot_password.php     # Password reset request
├── logout.php              # Session destruction
├── dashboard.php           # Customer dashboard with statistics
├── profile.php             # Profile management and editing
├── cart.php                # Shopping cart with quantity controls
├── checkout.php            # Secure checkout process
├── orders.php              # Order history listing
├── order_details.php       # Individual order details view
├── wishlist.php            # Saved favorite products
├── reviews.php             # User's product reviews
├── set_currency.php        # Currency switching handler
├── add_to_cart.php         # AJAX cart addition
└── update_cart.php         # AJAX cart updates
```

## 📁 admin/
**Administrative panel for store management**
```
admin/
└── index.php               # Admin dashboard with statistics and navigation
```

## 📁 assets/
**Static resources for styling and functionality**

### 📁 assets/css/
```
css/
└── custom.css              # Custom animations, effects, and responsive styles
```

### 📁 assets/js/
```
js/
└── main.js                 # Enhanced JavaScript with notifications, loading states
```

### 📁 assets/images/
```
images/
└── demo-product.jpg        # Fallback image for missing product images
```

## 📁 includes/
**Reusable components and templates**
```
includes/
├── header.php              # Navigation bar with dropdowns, currency selector
└── footer.php              # Footer with links, newsletter, contact info
```

## 📁 documentation/
**Project documentation and guides**
```
documentation/
├── project-structure.md    # This file - complete project structure
├── features.md             # Detailed feature documentation
├── installation.md         # Setup and installation guide
└── api-endpoints.md        # AJAX endpoints and API documentation
```

## 🔗 Page Relationships

### **Public Pages**
- `index.php` → Product listing, search, filtering
- `pages/login.php` → User authentication
- `pages/register.php` → New user registration

### **Customer Pages** (Requires Login)
- `pages/dashboard.php` → Account overview
- `pages/profile.php` → Personal information management
- `pages/cart.php` → Shopping cart management
- `pages/checkout.php` → Order placement
- `pages/orders.php` → Order history
- `pages/order_details.php` → Individual order view
- `pages/wishlist.php` → Saved products
- `pages/reviews.php` → Product reviews

### **Admin Pages** (Requires Admin Role)
- `admin/index.php` → Administrative dashboard

### **AJAX Handlers**
- `pages/set_currency.php` → Currency switching
- `pages/add_to_cart.php` → Add products to cart
- `pages/update_cart.php` → Update cart quantities

## 🎨 Design System
- **Framework**: Tailwind CSS 3.0
- **Icons**: Font Awesome 6.5.1
- **Colors**: Blue/Purple gradient theme
- **Layout**: Mobile-first responsive design
- **Components**: Modern cards, buttons, forms with hover effects