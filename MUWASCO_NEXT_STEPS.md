# 🔧 MUWASCO - NEXT DEBUGGING STEPS

## ✅ **CONFIG ANALYSIS - LOOKS CORRECT!**

Your database configuration appears correct:
```php
$host = 'localhost:3306';     // ✅ Good for MariaDB
$username = 'maggie_hrm';     // ✅ Matches server user  
$password = 'hrm12345678#';   // ✅ Your actual password
$database = 'maggie_hrm';     // ✅ Your database name
```

Since config looks right but 500 error persists, let's find the real issue.

---

## 🚨 **STEP 1: UPLOAD DEBUG FILE IMMEDIATELY**

Create `debug.php` in your `/httpdocs/HR/` directory:

```php
<?php
// MUWASCO URGENT DEBUG - Find the real 500 error cause
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>🔧 MUWASCO LIVE DEBUG</h2>";
echo "<p><strong>Testing your exact config...</strong></p>";

// Test 1: Basic PHP functionality
echo "<h3>1. PHP Status:</h3>";
echo "• PHP Version: " . phpversion() . "<br>";
echo "• Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "• Max Execution Time: " . ini_get('max_execution_time') . "s<br>";

// Test 2: Required extensions
echo "<h3>2. Required Extensions:</h3>";
$extensions = ['mysqli', 'json', 'mbstring', 'curl'];
foreach($extensions as $ext) {
    $status = extension_loaded($ext) ? "✅ Loaded" : "❌ MISSING";
    echo "• {$ext}: {$status}<br>";
}

// Test 3: File permissions and existence
echo "<h3>3. File Check:</h3>";
$files = ['config.php', 'login.php', 'index.php'];
foreach($files as $file) {
    if(file_exists($file)) {
        $readable = is_readable($file) ? "✅" : "❌ Not readable";
        $size = filesize($file);
        echo "• {$file}: ✅ Exists, {$readable}, {$size} bytes<br>";
    } else {
        echo "• {$file}: ❌ MISSING<br>";
    }
}

// Test 4: Database connection with your exact config
echo "<h3>4. Database Test (Your Exact Config):</h3>";
$host = 'localhost:3306';
$username = 'maggie_hrm';
$password = 'hrm12345678#';
$database = 'maggie_hrm';

try {
    echo "• Attempting connection to {$database}@{$host}...<br>";
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        echo "❌ CONNECTION FAILED: " . $conn->connect_error . "<br>";
        echo "• Error Number: " . $conn->connect_errno . "<br>";
    } else {
        echo "✅ DATABASE CONNECTION SUCCESS!<br>";
        echo "• Server Info: " . $conn->server_info . "<br>";
        echo "• Host Info: " . $conn->host_info . "<br>";
        
        // Test if tables exist
        $result = $conn->query("SHOW TABLES");
        if($result && $result->num_rows > 0) {
            echo "• Tables found: " . $result->num_rows . "<br>";
            while($row = $result->fetch_array()) {
                echo "  - {$row[0]}<br>";
            }
            
            // Test users table specifically
            $users_check = $conn->query("SELECT COUNT(*) as count FROM users");
            if($users_check) {
                $user_count = $users_check->fetch_assoc();
                echo "• Users in database: " . $user_count['count'] . "<br>";
            }
        } else {
            echo "❌ NO TABLES FOUND - Database might be empty<br>";
        }
        $conn->close();
    }
} catch(Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "<br>";
}

// Test 5: Try to include config.php
echo "<h3>5. Config.php Include Test:</h3>";
try {
    ob_start();
    include 'config.php';
    $output = ob_get_clean();
    echo "✅ config.php included successfully<br>";
    if(isset($conn)) {
        echo "✅ \$conn variable created<br>";
    } else {
        echo "❌ \$conn variable NOT created<br>";
    }
} catch(Exception $e) {
    echo "❌ Error including config.php: " . $e->getMessage() . "<br>";
} catch(Error $e) {
    echo "❌ Fatal error in config.php: " . $e->getMessage() . "<br>";
}

// Test 6: Simple login.php test
echo "<h3>6. Login.php Test:</h3>";
if(file_exists('login.php')) {
    echo "✅ login.php exists<br>";
    
    // Check if we can read the first few lines
    $login_content = file_get_contents('login.php', false, null, 0, 500);
    if($login_content) {
        echo "✅ login.php is readable<br>";
        if(strpos($login_content, '<?php') !== false) {
            echo "✅ login.php has PHP opening tag<br>";
        } else {
            echo "❌ login.php missing PHP opening tag<br>";
        }
    } else {
        echo "❌ Cannot read login.php content<br>";
    }
} else {
    echo "❌ login.php NOT FOUND<br>";
}

echo "<h3>🎯 Next Steps:</h3>";
echo "1. Check the results above for any ❌ errors<br>";
echo "2. If database connection works, the issue is in your PHP code<br>";
echo "3. If database fails, check your Plesk database settings<br>";
echo "4. Delete this debug.php file when done<br>";
?>
```

**Upload this to your server and visit:** `https://muwasco.co.ke/HR/debug.php`

---

## 🎯 **POSSIBLE ISSUES (Since Config Looks Right):**

### **Issue 1: Database Not Imported**
- Database exists but tables missing
- Solution: Import your `hr_management.sql` file

### **Issue 2: PHP Code Error**
- Syntax error in login.php or other files
- Solution: Debug file will show this

### **Issue 3: File Permissions**
- PHP files not readable by web server
- Solution: Set 644 permissions

### **Issue 4: Missing Files**
- Some PHP files not uploaded properly
- Solution: Re-upload all files

### **Issue 5: nginx Configuration**
- nginx not processing PHP files correctly
- Solution: Check with hosting provider

---

## ⚡ **IMMEDIATE ACTIONS:**

1. **Upload debug.php** and run it
2. **Check the output** for any ❌ errors
3. **If database connection works** but tables missing → Import SQL file
4. **If PHP errors shown** → Fix the specific error
5. **Contact me** with the debug.php output

---

## 🚨 **MOST LIKELY SCENARIO:**

Since your config looks correct, the issue is probably:
1. **Database is empty** (no tables imported)
2. **PHP syntax error** in one of your files
3. **File permission** issue

**The debug file will tell us exactly what's wrong!** 🚀