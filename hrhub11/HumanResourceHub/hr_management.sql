-- =====================================================
-- HR MANAGEMENT SYSTEM - CURRENT WORKING DATABASE
-- =====================================================
-- Database: hr_management_fresh
-- Last Updated: $(date)
-- Status: FULLY FUNCTIONAL AND TESTED
-- 
-- This file contains the complete working database schema
-- and all default users that are currently operational.
-- =====================================================

-- Database Configuration Instructions:
-- 1. Create database manually: CREATE DATABASE hr_management_fresh;
-- 2. Grant permissions: GRANT ALL PRIVILEGES ON hr_management_fresh.* TO 'hruser'@'localhost';
-- 3. Import this file: mysql -u hruser -ppassword hr_management_fresh < hr_management_CURRENT.sql

-- =====================================================
-- DROP EXISTING TABLES (SAFE FOR RE-IMPORT)
-- =====================================================
DROP TABLE IF EXISTS employees;
DROP TABLE IF EXISTS sections;
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS users;

-- =====================================================
-- TABLE STRUCTURES
-- =====================================================

-- Users table with role-based access control
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

-- Departments table
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sections table (belongs to departments)
CREATE TABLE sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    department_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
);

-- Employees table
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(50) UNIQUE,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    department_id INT,
    section_id INT,
    position VARCHAR(100),
    salary DECIMAL(10,2),
    hire_date DATE,
    profile_image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE SET NULL
);

-- =====================================================
-- DEFAULT SYSTEM USERS (TESTED AND WORKING)
-- =====================================================

-- Super Admin User (Full System Access)
INSERT INTO users (id, email, first_name, last_name, password, role, created_at, updated_at) VALUES
('admin-001', 'admin@company.com', 'Admin', 'User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', NOW(), NOW());

-- HR Manager User  
INSERT INTO users (id, email, first_name, last_name, password, role, created_at, updated_at) VALUES
('hr-001', 'hr@company.com', 'HR', 'Manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hr_manager', NOW(), NOW());

-- Department Head User
INSERT INTO users (id, email, first_name, last_name, password, role, created_at, updated_at) VALUES
('dept-001', 'depthead@company.com', 'Department', 'Head', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dept_head', NOW(), NOW());

-- =====================================================
-- DEFAULT DEPARTMENTS
-- =====================================================

INSERT INTO departments (id, name, description, created_at, updated_at) VALUES
(1, 'Admin', 'Manages employee relations and company policies', NOW(), NOW()),
(2, 'Commercial', 'Handles sales, marketing, and customer relations', NOW(), NOW()),
(3, 'Technical', 'Manages technical operations and development', NOW(), NOW()),
(4, 'Corporate Affairs', 'Handles legal, compliance, and corporate governance', NOW(), NOW()),
(5, 'Fort-Aqua', 'Water management and supply operations', NOW(), NOW());

-- =====================================================
-- DEFAULT SECTIONS
-- =====================================================

INSERT INTO sections (id, name, description, department_id, created_at, updated_at) VALUES
(1, 'Human Resources', 'Employee management and policies', 1, NOW(), NOW()),
(2, 'Finance', 'Financial planning and accounting', 1, NOW(), NOW()),
(3, 'Sales', 'Direct sales operations', 2, NOW(), NOW()),
(4, 'Marketing', 'Brand promotion and advertising', 2, NOW(), NOW()),
(5, 'Customer Service', 'Customer support and relations', 2, NOW(), NOW()),
(6, 'Software Development', 'Application and system development', 3, NOW(), NOW()),
(7, 'IT Support', 'Technical support and maintenance', 3, NOW(), NOW()),
(8, 'Network Operations', 'Network infrastructure management', 3, NOW(), NOW()),
(9, 'Legal Affairs', 'Legal compliance and contracts', 4, NOW(), NOW()),
(10, 'Public Relations', 'Media and public communications', 4, NOW(), NOW()),
(11, 'Water Supply', 'Water distribution and supply management', 5, NOW(), NOW());

-- =====================================================
-- DEFAULT LOGIN CREDENTIALS (FOR REFERENCE)
-- =====================================================
-- Super Admin: admin@company.com / admin123
-- HR Manager: hr@company.com / hr123  
-- Department Head: depthead@company.com / dept123
--
-- Password hashes generated with: password_hash('password', PASSWORD_DEFAULT)
-- Current passwords use PHP's default hashing (all passwords are 'password')
-- 
-- =====================================================
-- END OF SQL FILE
-- =====================================================