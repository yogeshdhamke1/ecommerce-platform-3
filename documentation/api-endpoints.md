# E-Commerce Platform - API Endpoints Documentation

## 🔗 AJAX Endpoints

### **Currency Management**

#### **Set Currency**
- **Endpoint**: `pages/set_currency.php`
- **Method**: `POST`
- **Content-Type**: `application/json`
- **Purpose**: Change user's selected currency

**Request Body:**
```json
{
    "currency": "USD"
}
```

**Response:**
```json
{
    "success": true
}
```

**Supported Currencies:**
- `USD`, `EUR`, `GBP`, `INR`, `JPY`

---

### **Shopping Cart Management**

#### **Add to Cart**
- **Endpoint**: `pages/add_to_cart.php`
- **Method**: `POST`
- **Content-Type**: `application/json`
- **Authentication**: Required (user session)

**Request Body:**
```json
{
    "product_id": 1,
    "quantity": 2
}
```

**Success Response:**
```json
{
    "success": true
}
```

**Error Response:**
```json
{
    "success": false,
    "error": "Not logged in"
}
```

#### **Update Cart**
- **Endpoint**: `pages/update_cart.php`
- **Method**: `POST`
- **Content-Type**: `application/json`
- **Authentication**: Required

**Request Body:**
```json
{
    "product_id": 1,
    "quantity": 3
}
```

**Note**: Set quantity to 0 to remove item from cart

---

### **Wishlist Management**

#### **Remove from Wishlist**
- **Endpoint**: `pages/remove_wishlist.php`
- **Method**: `POST`
- **Content-Type**: `application/json`
- **Authentication**: Required

**Request Body:**
```json
{
    "product_id": 1
}
```

---

## 📄 Page Endpoints

### **Public Pages**

#### **Homepage**
- **URL**: `/index.php`
- **Parameters**:
  - `category` (optional) - Filter by category ID
  - `search` (optional) - Search products by name/description
- **Example**: `index.php?category=1&search=diamond`

#### **Product Details**
- **URL**: `pages/product.php`
- **Parameters**:
  - `id` (required) - Product ID
- **Example**: `pages/product.php?id=1`

---

### **Authentication Pages**

#### **Login**
- **URL**: `pages/login.php`
- **Method**: `GET` (form display), `POST` (login submission)
- **POST Data**:
  - `email` - User email
  - `password` - User password

#### **Register**
- **URL**: `pages/register.php`
- **Method**: `GET` (form display), `POST` (registration)
- **POST Data**:
  - `username` - Unique username
  - `email` - User email
  - `password` - User password
  - `full_name` - User's full name

#### **Forgot Password**
- **URL**: `pages/forgot_password.php`
- **Method**: `GET` (form display), `POST` (reset request)
- **POST Data**:
  - `email` - User email for reset

#### **Logout**
- **URL**: `pages/logout.php`
- **Method**: `GET`
- **Action**: Destroys session and redirects to homepage

---

### **Customer Dashboard Pages**

#### **Dashboard**
- **URL**: `pages/dashboard.php`
- **Authentication**: Required
- **Features**: Account statistics, recent orders, quick actions

#### **Profile Management**
- **URL**: `pages/profile.php`
- **Method**: `GET` (display), `POST` (update)
- **POST Data**:
  - `full_name` - User's full name
  - `phone` - Phone number
  - `address` - User address

#### **Orders**
- **URL**: `pages/orders.php`
- **Authentication**: Required
- **Features**: List all user orders with status

#### **Order Details**
- **URL**: `pages/order_details.php`
- **Parameters**:
  - `id` (required) - Order ID
- **Authentication**: Required (must own order)

#### **Shopping Cart**
- **URL**: `pages/cart.php`
- **Authentication**: Required
- **Features**: View cart items, update quantities, proceed to checkout

#### **Checkout**
- **URL**: `pages/checkout.php`
- **Method**: `GET` (form display), `POST` (place order)
- **Authentication**: Required
- **POST Data**:
  - `shipping_address` - Delivery address
  - `payment_method` - Payment method selection

#### **Wishlist**
- **URL**: `pages/wishlist.php`
- **Authentication**: Required
- **Features**: View saved products, add to cart, remove items

#### **Reviews**
- **URL**: `pages/reviews.php`
- **Authentication**: Required
- **Features**: View user's product reviews and ratings

---

### **Admin Pages**

#### **Admin Dashboard**
- **URL**: `admin/index.php`
- **Authentication**: Required (admin role)
- **Features**: Sales statistics, user management, order overview

---

## 🔒 Authentication Requirements

### **Session-Based Authentication**
- **Session Variable**: `$_SESSION['user_id']`
- **Admin Check**: `$_SESSION['is_admin']`
- **Redirect**: Unauthenticated users redirected to `pages/login.php`

### **Protected Endpoints**
All customer and admin endpoints require active user session:
- Dashboard pages
- Cart operations
- Order management
- Profile management
- Admin panel

---

## 📊 Response Formats

### **AJAX Responses**
All AJAX endpoints return JSON responses:

**Success Format:**
```json
{
    "success": true,
    "data": {} // Optional additional data
}
```

**Error Format:**
```json
{
    "success": false,
    "error": "Error message description"
}
```

### **Common Error Messages**
- `"Not logged in"` - User session required
- `"Invalid product ID"` - Product not found
- `"Invalid request method"` - Wrong HTTP method
- `"Network error occurred"` - Connection issues
- `"Failed to update cart"` - Database operation failed

---

## 🛠️ JavaScript Integration

### **Currency Switching**
```javascript
fetch('pages/set_currency.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({currency: 'EUR'})
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        location.reload();
    }
});
```

### **Add to Cart**
```javascript
fetch('pages/add_to_cart.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        product_id: productId, 
        quantity: 1
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        showNotification('Product added to cart!', 'success');
    }
});
```

### **Update Cart Quantity**
```javascript
fetch('pages/update_cart.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        product_id: productId,
        quantity: newQuantity
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        location.reload();
    }
});
```

---

## 🔍 URL Parameters

### **Search and Filtering**
- `category` - Filter products by category ID (1-5)
- `search` - Search products by name or description
- `id` - Specific item ID for details pages

### **Pagination** (Ready for implementation)
- `page` - Page number for pagination
- `limit` - Items per page limit

### **Sorting** (Ready for implementation)
- `sort` - Sort field (price, name, date)
- `order` - Sort direction (asc, desc)

---

## 📱 Mobile API Considerations

### **Touch-Friendly Responses**
- Optimized for mobile interactions
- Fast response times
- Minimal data transfer
- Error handling for poor connections

### **Progressive Enhancement**
- Graceful degradation without JavaScript
- Form fallbacks for AJAX operations
- Mobile-optimized UI responses