# 🚨 CRITICAL: MIXED DATABASE CONNECTIONS FOUND

## ⚡ **THE EXACT PROBLEM:**

Your HR system has **MIXED database connections**:

### **MySQLi Files:**
- `config.php` → `$conn = new mysqli()`
- `login.php` → `$conn->query()`, `$result->fetch_assoc()`

### **PDO Files (BROKEN):**
- `dashboard.php` → `$pdo = getConnection()` ❌ **FUNCTION MISSING**
- `departments.php` → `$pdo = getConnection()` ❌ **FUNCTION MISSING**  
- `users.php` → `$pdo = getConnection()` ❌ **FUNCTION MISSING**
- `employees.php` → `$pdo = getConnection()` ❌ **FUNCTION MISSING**

**This is why you get 500 errors - the `getConnection()` function doesn't exist!**

---

## 🔧 **SOLUTION 1: ADD PDO FUNCTION TO CONFIG.PHP**

Update your `config.php` to support BOTH MySQLi and PDO:

```php
<?php
/**
 * MUWASCO HR SYSTEM - MIXED MYSQLI/PDO SUPPORT
 * Fixed for both connection types
 */

// Start session
session_start();

// Database credentials
$host = 'localhost:3306';
$username = 'maggie_hrm';
$password = 'hrm12345678#';
$database = 'maggie_hrm';

// MySQLi connection (for login.php)
try {
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        throw new Exception("MySQLi connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
    die("Database connection error. Please contact administrator.");
}

// PDO connection function (for dashboard.php, users.php, etc.)
function getConnection() {
    global $host, $username, $password, $database;
    
    try {
        // Remove port from host for PDO
        $pdo_host = str_replace(':3306', '', $host);
        $dsn = "mysql:host={$pdo_host};port=3306;dbname={$database};charset=utf8mb4";
        
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        
        return $pdo;
        
    } catch (PDOException $e) {
        error_log("PDO Error: " . $e->getMessage());
        die("Database connection error. Please contact administrator.");
    }
}

// Helper functions
function sanitizeInput($input) {
    global $conn;
    return $conn->real_escape_string(trim($input));
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
    return $_SESSION['user_id'];
}

function getCurrentUser() {
    global $conn;
    $userId = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = '$userId'";
    $result = $conn->query($sql);
    return $result ? $result->fetch_assoc() : null;
}

// Debug mode (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
```

---

## 🔧 **SOLUTION 2: CONVERT ALL TO MYSQLI (Simpler)**

If you prefer, convert PDO files to use MySQLi instead:

### **Update dashboard.php:**
Replace this:
```php
$pdo = getConnection();
$stmt = $pdo->query("SELECT COUNT(*) as count FROM employees WHERE employee_status = 'active'");
$totalEmployees = $stmt->fetch()['count'];
```

With this:
```php
$result = $conn->query("SELECT COUNT(*) as count FROM employees WHERE employee_status = 'active'");
$totalEmployees = $result->fetch_assoc()['count'];
```

---

## 🔧 **QUICK TEST FILE**

Create `connection_test.php` to test both connections:

```php
<?php
require_once 'config.php';

echo "<h2>🔧 MIXED CONNECTION TEST</h2>";

// Test MySQLi
echo "<h3>1. MySQLi Test:</h3>";
if (isset($conn)) {
    if ($conn->ping()) {
        echo "✅ MySQLi connection works<br>";
        $result = $conn->query("SHOW TABLES");
        echo "• Tables found: " . ($result ? $result->num_rows : 0) . "<br>";
    } else {
        echo "❌ MySQLi connection failed<br>";
    }
} else {
    echo "❌ MySQLi \$conn not found<br>";
}

// Test PDO
echo "<h3>2. PDO Test:</h3>";
try {
    $pdo = getConnection();
    if ($pdo) {
        echo "✅ PDO connection works<br>";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll();
        echo "• Tables found: " . count($tables) . "<br>";
    } else {
        echo "❌ PDO connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ PDO error: " . $e->getMessage() . "<br>";
}

// Test specific queries
echo "<h3>3. Query Tests:</h3>";
try {
    // MySQLi query
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    if ($result) {
        $count = $result->fetch_assoc();
        echo "✅ MySQLi users count: " . $count['count'] . "<br>";
    }
    
    // PDO query
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch();
    echo "✅ PDO users count: " . $count['count'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Query error: " . $e->getMessage() . "<br>";
}
?>
```

**Visit:** `https://muwasco.co.ke/HR/connection_test.php`

---

## 🎯 **RECOMMENDED APPROACH:**

**Use Solution 1** - Add the `getConnection()` function to your `config.php`. This will:

1. ✅ Keep your existing login.php working (MySQLi)
2. ✅ Fix dashboard.php and other PDO files
3. ✅ Support both connection types
4. ✅ Minimal code changes needed

**This will immediately fix your 500 errors!** 🚀