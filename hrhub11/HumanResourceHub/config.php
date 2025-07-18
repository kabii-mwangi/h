<?php
/**
 * =====================================================
 * HR MANAGEMENT SYSTEM - CURRENT WORKING CONFIGURATION
 * =====================================================
 * Database: hr_management_fresh
 * Status: FULLY FUNCTIONAL AND TESTED
 * Last Updated: $(date)
 * =====================================================
 */

// Database configuration (CURRENT WORKING SETTINGS)
$host = 'localhost';
$username = 'hruser';
$password = 'password';
$database = 'hr_management_fresh';  // Updated to current working database

// Create MySQL connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");

/**
 * =====================================================
 * IMPORTANT NOTES:
 * =====================================================
 * 
 * 1. This configuration connects to 'hr_management_fresh' database
 * 2. Database user 'hruser' has been granted proper permissions
 * 3. MySQL server must be running for connections to work
 * 4. All default users are available with standard login credentials
 * 
 * Default Login Credentials:
 * - Super Admin: admin@company.com / admin123
 * - HR Manager: hr@company.com / hr123
 * - Department Head: depthead@company.com / dept123
 * 
 * =====================================================
 */
?>