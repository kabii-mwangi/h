# HR Management System - MySQL Version

A comprehensive Human Resource Management System built with PHP and MySQL, featuring employee management, organizational hierarchy, and role-based access control.

## Features

- **Role-based access control** with 6 different user levels
- **Employee management** with full CRUD operations
- **Department and section management** 
- **User management** for super administrators
- **Beautiful sky blue theme** throughout the application
- **Responsive design** that works on all devices

## Installation

### For Hosting Providers (Production)

1. **Create Database**: Manually create a MySQL database named `hr_management` in your hosting control panel
2. **Import SQL**: Use the `hr_management.sql` file to import the database structure and sample data
   - If you get "DROP DATABASE statements are disabled" error, that's normal for shared hosting
   - The SQL file is designed to work without database creation permissions
3. **Configuration**: Update the database credentials in `config.php`:
   ```php
   $host = 'your-mysql-host';        // Usually 'localhost'
   $username = 'your-mysql-username';
   $password = 'your-mysql-password';
   $database = 'hr_management';      // Your database name
   ```
4. **Upload Files**: Upload all PHP files to your web server's public directory
5. **Access**: Navigate to your domain to start using the system

### Troubleshooting

- **"DROP DATABASE disabled"**: This is normal on shared hosting. Just create the database manually first.
- **Connection failed**: Double-check your database credentials in `config.php`
- **Table not found**: Make sure the SQL import completed successfully

## Default Login Credentials

**Note: All default accounts use the same password for simplicity**

- **Super Admin**: admin@company.com / admin123
- **HR Manager**: hr@company.com / admin123  
- **Department Head**: depthead@company.com / admin123

## File Structure

```
├── config.php              # Database configuration and helper functions
├── hr_management.sql        # MySQL database schema and sample data
├── index.php               # Redirects to login page
├── login.php               # User authentication
├── logout.php              # Session termination
├── dashboard.php           # Main dashboard
├── employees.php           # Employee management
├── departments.php         # Department and section management
├── users.php               # User management (super admin only)
├── style.css               # Application styling
└── README.md               # This file
```

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

## Role Permissions

1. **Super Admin**: Full system access including user management
2. **HR Manager**: Employee and department management
3. **Department Head**: View department employees
4. **Section Head**: View section employees
5. **Manager**: Basic management privileges
6. **Employee**: View own profile only

## Theme

The application features a beautiful sky blue color scheme (#0ea5e9) with professional gradients and clean, modern interface design.