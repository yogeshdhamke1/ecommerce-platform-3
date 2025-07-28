# Test Credentials for E-Commerce Platform

## 🔑 Login Credentials

### **Admin Account**
- **Email**: `admin@test.com`
- **Password**: `password`
- **Role**: Administrator
- **Access**: Full admin panel access

### **Customer Account 1**
- **Email**: `test@test.com`
- **Password**: `password`
- **Username**: `testuser`
- **Role**: Customer

### **Customer Account 2**
- **Email**: `customer@test.com`
- **Password**: `password`
- **Username**: `customer1`
- **Role**: Customer

## 💱 Currency Settings

### **Default Currency**
- **Base Currency**: Indian Rupee (INR ₹)
- **Exchange Rates** (from INR):
  - INR: ₹1.00 (Base)
  - USD: $0.012
  - EUR: €0.011
  - GBP: £0.0095
  - JPY: ¥1.80

### **Product Prices in INR**
- Diamond Ring: ₹1,08,000
- Smartphone: ₹66,500
- Laptop: ₹1,08,000
- Designer Dress: ₹16,600
- Coffee Table: ₹33,200

## 🧪 Testing Instructions

### **1. Admin Testing**
1. Login with `admin@test.com` / `password`
2. Access admin panel at `/admin/index.php`
3. View dashboard statistics
4. Manage products and orders

### **2. Customer Testing**
1. Login with `test@test.com` / `password`
2. Browse products (prices in INR)
3. Add items to cart
4. Test currency conversion
5. Complete checkout process

### **3. Currency Testing**
1. Default currency shows as INR
2. Switch between currencies using dropdown
3. Verify price conversions
4. Check order currency persistence

## 📱 Quick Test Flow

1. **Registration**: Create new account or use test credentials
2. **Shopping**: Browse products, add to cart
3. **Currency**: Switch between INR, USD, EUR, GBP, JPY
4. **Checkout**: Complete order with INR as default
5. **Dashboard**: View order history and profile
6. **Admin**: Login as admin to manage orders

## 🎯 Key Features to Test

- ✅ INR as default currency
- ✅ Multi-currency conversion
- ✅ User authentication
- ✅ Shopping cart functionality
- ✅ Order management
- ✅ Admin dashboard
- ✅ Responsive design