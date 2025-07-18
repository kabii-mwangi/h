-- HR Management System MySQL Database Schema
-- NOTE: If "DROP DATABASE" is disabled on your hosting, 
-- manually create a database named 'hr_management' first, then run this script

-- Uncomment the following lines if your hosting allows database creation:
-- DROP DATABASE IF EXISTS hr_management;
-- CREATE DATABASE hr_management;
-- USE hr_management;

-- Drop tables if they exist (safer for hosting environments)
DROP TABLE IF EXISTS employees;
DROP TABLE IF EXISTS sections;
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id VARCHAR(50) PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    password VARCHAR(255),
    role ENUM('super_admin', 'hr_manager', 'dept_head', 'section_head', 'manager', 'employee') DEFAULT 'employee',
    phone VARCHAR(20),
    address TEXT,
    profile_image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create departments table
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create sections table
CREATE TABLE sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    department_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
);

-- Create employees table
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    date_of_birth DATE,
    hire_date DATE NOT NULL,
    department_id INT,
    section_id INT,
    employee_type ENUM('officer', 'section_head', 'manager', 'dept_head', 'managing_director', 'bod_chairman') DEFAULT 'officer',
    status ENUM('active', 'inactive', 'terminated') DEFAULT 'active',
    user_id VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default departments
INSERT INTO departments (name, description) VALUES
('Admin', 'Manages employee relations and company policies'),
('Commercial', 'Handles sales, marketing, and customer relations'),
('Technical', 'Manages technical operations and development'),
('Corporate Affairs', 'Handles legal, compliance, and corporate governance'),
('Fort-Aqua', 'Water management and supply operations');

-- Insert default sections
INSERT INTO sections (name, description, department_id) VALUES
('Human Resources', 'Employee management and policies', 1),
('Finance', 'Financial planning and accounting', 1),
('Sales', 'Direct sales operations', 2),
('Marketing', 'Brand promotion and advertising', 2),
('Customer Service', 'Customer support and relations', 2),
('Software Development', 'Application and system development', 3),
('IT Support', 'Technical support and maintenance', 3),
('Network Operations', 'Network infrastructure management', 3),
('Legal Affairs', 'Legal compliance and contracts', 4),
('Public Relations', 'Media and public communications', 4),
('Water Supply', 'Water distribution and supply management', 5);

-- Insert default users with hashed passwords
-- All passwords are the same: admin123
INSERT INTO users (id, first_name, last_name, email, password, role, created_at, updated_at) VALUES
('admin-001', 'Admin', 'User', 'admin@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', NOW(), NOW()),
('hr-001', 'HR', 'Manager', 'hr@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hr_manager', NOW(), NOW()),
('dept-001', 'Department', 'Head', 'depthead@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dept_head', NOW(), NOW());

-- Insert sample employees
INSERT INTO employees (employee_id, first_name, last_name, email, phone, hire_date, department_id, section_id, employee_type, user_id) VALUES
('EMP001', 'John', 'Doe', 'john.doe@company.com', '123-456-7890', '2023-01-15', 1, 1, 'manager', NULL),
('EMP002', 'Jane', 'Smith', 'jane.smith@company.com', '123-456-7891', '2023-02-20', 2, 3, 'section_head', NULL),
('EMP003', 'Mike', 'Johnson', 'mike.johnson@company.com', '123-456-7892', '2023-03-10', 3, 6, 'officer', NULL);

-- Create indexes for better performance
CREATE INDEX idx_employees_department ON employees(department_id);
CREATE INDEX idx_employees_section ON employees(section_id);
CREATE INDEX idx_employees_status ON employees(status);
CREATE INDEX idx_employees_type ON employees(employee_type);
CREATE INDEX idx_sections_department ON sections(department_id);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);

/*
IMPORTANT: Default login credentials (all passwords are 'admin123'):
- Super Admin: admin@company.com / admin123
- HR Manager: hr@company.com / admin123  
- Department Head: depthead@company.com / admin123

HOSTING SETUP:
1. Create a MySQL database named 'hr_management' in your hosting control panel
2. Import this SQL file into that database
3. Update database credentials in config.php
4. Upload all PHP files to your web server

NOTE: If your hosting provider shows "DROP DATABASE statements are disabled", 
that's normal. Just create the database manually first, then import this file.
*/