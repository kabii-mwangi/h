# 🔧 MYSQLI-SPECIFIC FIX FOR MUWASCO

## ✅ **CONFIRMED: Your System Uses MySQLi (Not PDO)**

Your HR system is entirely MySQLi-based:
- `new mysqli()` connections
- `$conn->query()` methods  
- `$result->fetch_assoc()` data retrieval

## 🚨 **MOST LIKELY MYSQLI ISSUES ON PLESK:**

### **Issue 1: Missing session_start()**
Your login.php uses `$_SESSION` but might not have `session_start()`

### **Issue 2: MySQLi Socket vs TCP**
Plesk sometimes has socket connection issues

### **Issue 3: Error Handling**
MySQLi errors might not display properly

---

## 🔧 **IMMEDIATE FIXES TO TRY:**

### **Fix 1: Update Your config.php**
Replace your config.php with this MySQLi-optimized version:

```php
<?php
/**
 * MUWASCO HR SYSTEM - MYSQLI OPTIMIZED FOR PLESK
 * MariaDB 10.11.5 | nginx 1.26.3 | PHP 8.4.7
 */

// Start session for user authentication
session_start();

// Database configuration
$host = 'localhost:3306';           // Your working host
$username = 'maggie_hrm';           // Your working user
$password = 'hrm12345678#';         // Your working password
$database = 'maggie_hrm';           // Your working database

// Create MySQLi connection with error handling
try {
    $conn = new mysqli($host, $username, $password, $database);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Set charset to UTF-8 for proper character handling
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    // Log error and show user-friendly message
    error_log("Database Error: " . $e->getMessage());
    die("Database connection error. Please contact administrator.");
}

// Helper function for input sanitization
function sanitizeInput($input) {
    global $conn;
    return $conn->real_escape_string(trim($input));
}

// Set error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
```

### **Fix 2: Update login.php header**
Add session_start() to login.php if missing:

```php
<?php
session_start();  // Add this line at the very top
require_once 'config.php';
// ... rest of your code
```

### **Fix 3: Try TCP Connection Instead of Socket**
If still having issues, modify config.php:

```php
// Try TCP connection instead of socket
$host = '127.0.0.1:3306';  // Instead of 'localhost:3306'
```

---

## 🔧 **ADVANCED MYSQLI DEBUG FILE**

Create this `mysqli_debug.php` to test your MySQLi setup:

```php
<?php
// MYSQLI SPECIFIC DEBUG FOR MUWASCO
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>🔧 MYSQLI DEBUG FOR MUWASCO</h2>";

// Test 1: MySQLi extension
echo "<h3>1. MySQLi Extension:</h3>";
if (extension_loaded('mysqli')) {
    echo "✅ MySQLi extension loaded<br>";
    echo "• MySQLi version: " . mysqli_get_client_info() . "<br>";
} else {
    echo "❌ MySQLi extension NOT loaded<br>";
}

// Test 2: Session functionality
echo "<h3>2. Session Test:</h3>";
session_start();
$_SESSION['test'] = 'working';
if (isset($_SESSION['test'])) {
    echo "✅ Sessions working<br>";
    unset($_SESSION['test']);
} else {
    echo "❌ Sessions NOT working<br>";
}

// Test 3: MySQLi connection with your exact config
echo "<h3>3. MySQLi Connection Test:</h3>";
$host = 'localhost:3306';
$username = 'maggie_hrm';
$password = 'hrm12345678#';
$database = 'maggie_hrm';

echo "• Connecting to: {$username}@{$host}/{$database}<br>";

try {
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        echo "❌ MySQLi Connection FAILED<br>";
        echo "• Error: " . $conn->connect_error . "<br>";
        echo "• Error Number: " . $conn->connect_errno . "<br>";
    } else {
        echo "✅ MySQLi Connection SUCCESS<br>";
        echo "• Server: " . $conn->server_info . "<br>";
        echo "• Host: " . $conn->host_info . "<br>";
        echo "• Character Set: " . $conn->character_set_name() . "<br>";
        
        // Test query
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            echo "• Tables found: " . $result->num_rows . "<br>";
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_row()) {
                    echo "  - {$row[0]}<br>";
                }
                
                // Test users table
                $users_result = $conn->query("SELECT COUNT(*) as count FROM users");
                if ($users_result) {
                    $count = $users_result->fetch_assoc();
                    echo "• Users in table: " . $count['count'] . "<br>";
                    
                    // Test a sample user
                    $sample_user = $conn->query("SELECT id, email, role FROM users LIMIT 1");
                    if ($sample_user && $sample_user->num_rows > 0) {
                        $user = $sample_user->fetch_assoc();
                        echo "• Sample user: " . $user['email'] . " (" . $user['role'] . ")<br>";
                    }
                }
            } else {
                echo "❌ Database is empty - no tables found<br>";
            }
        } else {
            echo "❌ Cannot query tables: " . $conn->error . "<br>";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
}

// Test 4: Try alternative connection
echo "<h3>4. Alternative Connection Test (127.0.0.1):</h3>";
try {
    $conn2 = new mysqli('127.0.0.1:3306', $username, $password, $database);
    if ($conn2->connect_error) {
        echo "❌ TCP Connection failed: " . $conn2->connect_error . "<br>";
    } else {
        echo "✅ TCP Connection works<br>";
        $conn2->close();
    }
} catch (Exception $e) {
    echo "❌ TCP Exception: " . $e->getMessage() . "<br>";
}

echo "<h3>🎯 Recommendations:</h3>";
echo "1. If MySQLi connection works but no tables → Import SQL file<br>";
echo "2. If connection fails → Check database credentials<br>";
echo "3. If sessions fail → Check PHP session configuration<br>";
echo "4. Update config.php with session_start() and error handling<br>";
?>
```

**Upload this and visit:** `https://muwasco.co.ke/HR/mysqli_debug.php`

---

## 🎯 **MOST LIKELY SOLUTION:**

1. **Add `session_start()`** to the top of config.php
2. **Import your SQL file** to populate tables
3. **Add proper error handling** to MySQLi connections

The MySQLi debug file will show exactly what's missing! 🚀