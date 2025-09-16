# Camp Of Coffee - Sales and Inventory Tracking System

A comprehensive Sales and Inventory Tracking System built with PHP and MySQL for managing coffee shop operations.

## Features

- **User Authentication**: Secure login system with password hashing
- **Role-based Access Control**: Admin and Cashier roles with different permissions
- **Dashboard**: Real-time statistics and overview of sales and inventory
- **Product Management**: Full CRUD operations for managing products
- **Point of Sale (POS)**: Intuitive interface for processing sales
- **Inventory Tracking**: Real-time stock management with low stock alerts
- **Sales Reports**: Detailed reports with date filtering and export options
- **User Management**: Admin-only feature to manage system users
- **Responsive Design**: Bootstrap-based UI that works on all devices

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- PDO PHP Extension

## Installation

1. Clone or download this repository to your web server directory (e.g., `htdocs` for XAMPP)

2. Create the database by running the SQL script:
   ```bash
   mysql -u root -p < database/schema.sql
   ```

3. Configure database connection in `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'camp_of_coffee');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. Access the system through your web browser:
   ```
   http://localhost/camp_of_coffee/
   ```

## Default Login Credentials

- **Username**: admin
- **Password**: admin123

## Directory Structure

```
camp_of_coffee/
├── ajax/                   # AJAX handlers
├── assets/                 # Images and static assets
├── config/                 # Configuration files
├── database/              # Database schema
├── includes/              # PHP includes and functions
├── dashboard.php          # Main dashboard
├── login.php             # Login page
├── logout.php            # Logout handler
├── products.php          # Product management
├── sales.php             # Point of Sale
├── reports.php           # Sales reports
├── users.php             # User management (Admin only)
└── index.php             # Entry point
```

## User Roles

### Admin
- Full access to all features
- Can manage users
- Can view all reports
- Can manage products and inventory

### Cashier
- Can process sales
- Can view products
- Can view own sales
- Cannot manage users

## Security Features

- Password hashing using bcrypt
- Session-based authentication
- Prepared statements to prevent SQL injection
- Role-based access control
- Input validation and sanitization

## Usage

### Processing a Sale
1. Navigate to the Sales page
2. Click on products to add them to cart
3. Adjust quantities as needed
4. Click "Process Sale" to complete the transaction

### Managing Products
1. Go to Products page
2. Add new products with name, category, price, and stock
3. Edit existing products
4. Monitor low stock items (< 20 units)

### Viewing Reports
1. Access Reports page
2. Select date range
3. View sales or product reports
4. Export to CSV for further analysis

## License

This project is licensed under the MIT License.
