# 🎯 MUWASCO SERVER - SPECIFIC 500 ERROR FIX

## 📋 **YOUR SERVER CONFIGURATION ANALYSIS**

Based on your server info, here's what I found:

```
✅ Server: nginx/1.26.3 (Good - nginx is running)
✅ Database: MariaDB 10.11.5 (Good - compatible with MySQL)
✅ PHP: 8.4.7 (Good - latest version)
✅ Extensions: mysqli, curl, mbstring (Good - all required)
✅ Database User: maggie_hrm@localhost
```

---

## 🚨 **IMMEDIATE FIX FOR YOUR CONFIG**

Your `config.php` needs to be updated with your **exact Plesk database credentials**:

### **STEP 1: Update config.php**

Replace your current `config.php` with:

```php
<?php
/**
 * MUWASCO HR SYSTEM - PLESK CONFIGURATION
 * Database: MariaDB 10.11.5
 * User: maggie_hrm@localhost
 */

// Database configuration for your Plesk server
$host = 'localhost';
$username = 'maggie_hrm';              // Your actual Plesk DB user
$password = 'YOUR_PLESK_DB_PASSWORD';   // Get this from Plesk Databases
$database = 'YOUR_PLESK_DB_NAME';       // Get this from Plesk Databases

// Create MySQL/MariaDB connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");  // Using utf8mb4 for full UTF-8 support

/* 
 * IMPORTANT: Replace the placeholders above with your actual:
 * - Database password (from Plesk)
 * - Database name (from Plesk)
 */
?>
```

---

## 🔍 **STEP 2: GET YOUR EXACT DATABASE DETAILS**

### **In Plesk Control Panel:**
1. **Go to "Websites & Domains"**
2. **Click "Databases"**
3. **You'll see something like:**
   - **Database Name:** `maggie_hrmanagement` or similar
   - **Database User:** `maggie_hrm` ✅ (confirmed)
   - **Password:** Click to view/reset

### **Update config.php with actual values:**
```php
$host = 'localhost';
$username = 'maggie_hrm';
$password = 'your_actual_password_from_plesk';
$database = 'your_actual_database_name_from_plesk';
```

---

## 🔧 **STEP 3: CREATE ENHANCED DEBUG FILE**

Create `debug.php` specifically for your MariaDB setup:

```php
<?php
// MUWASCO DEBUG - MariaDB/nginx/PHP 8.4.7
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>🔧 MUWASCO SERVER DEBUG</h2>";
echo "<strong>Server Info:</strong><br>";
echo "• PHP Version: " . phpversion() . "<br>";
echo "• Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "• Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "• Current Directory: " . getcwd() . "<br>";
echo "• Script Path: " . __FILE__ . "<br><br>";

echo "<strong>Required Extensions:</strong><br>";
$extensions = ['mysqli', 'json', 'mbstring', 'curl'];
foreach($extensions as $ext) {
    $status = extension_loaded($ext) ? "✅" : "❌";
    echo "• {$ext}: {$status}<br>";
}

echo "<br><strong>Files in HR Directory:</strong><br>";
$files = scandir('.');
foreach($files as $file) {
    if($file != '.' && $file != '..') {
        $readable = is_readable($file) ? "✅" : "❌";
        $size = is_file($file) ? " (" . filesize($file) . " bytes)" : "";
        echo "• {$file}: {$readable}{$size}<br>";
    }
}

echo "<br><strong>🗄️ MariaDB Connection Test:</strong><br>";
if(file_exists('config.php')) {
    echo "• config.php: ✅ Found<br>";
    
    // Capture any errors
    ob_start();
    $error_occurred = false;
    
    try {
        include 'config.php';
        
        if(isset($conn)) {
            echo "• Connection object: ✅ Created<br>";
            
            if($conn->ping()) {
                echo "• Database ping: ✅ SUCCESS<br>";
                echo "• Connected as: {$username}@{$host}<br>";
                echo "• Database: {$database}<br>";
                echo "• MariaDB version: " . $conn->server_info . "<br>";
                
                // Test if our tables exist
                $result = $conn->query("SHOW TABLES");
                if($result) {
                    echo "• Tables found: " . $result->num_rows . "<br>";
                    while($row = $result->fetch_array()) {
                        echo "  - {$row[0]}<br>";
                    }
                } else {
                    echo "• ❌ No tables found or access denied<br>";
                }
                
            } else {
                echo "• ❌ Database ping FAILED<br>";
                echo "• Error: " . $conn->connect_error . "<br>";
                $error_occurred = true;
            }
        } else {
            echo "• ❌ Connection object not created<br>";
            $error_occurred = true;
        }
        
    } catch(Exception $e) {
        echo "• ❌ Exception: " . $e->getMessage() . "<br>";
        $error_occurred = true;
    } catch(Error $e) {
        echo "• ❌ Fatal Error: " . $e->getMessage() . "<br>";
        $error_occurred = true;
    }
    
    $output = ob_get_clean();
    echo $output;
    
} else {
    echo "• ❌ config.php NOT FOUND<br>";
}

echo "<br><strong>Next Steps:</strong><br>";
echo "1. Update config.php with correct Plesk database credentials<br>";
echo "2. Import your hr_management.sql file to the database<br>";
echo "3. Test login with: admin@company.com / admin123<br>";
echo "4. Delete this debug.php file when done<br>";
?>
```

**Visit:** `https://muwasco.co.ke/HR/debug.php`

---

## 🎯 **STEP 4: IMPORT YOUR DATABASE**

### **Via Plesk phpMyAdmin:**
1. **Go to Plesk → Databases → phpMyAdmin**
2. **Login with `maggie_hrm` and your password**
3. **Select your database**
4. **Go to "Import" tab**
5. **Upload your `hr_management.sql` file**
6. **Click "Go"**

---

## ⚡ **COMMON ISSUES & FIXES FOR YOUR SETUP**

### **🔹 Issue 1: Wrong Database Name/Password**
**Solution:** Get exact credentials from Plesk Databases section

### **🔹 Issue 2: MariaDB vs MySQL Differences**
**Solution:** Your code should work fine - MariaDB is MySQL-compatible

### **🔹 Issue 3: PHP 8.4.7 Compatibility**
**Solution:** Your code should work - PHP 8.4 is latest

### **🔹 Issue 4: nginx Configuration**
**Solution:** Ensure PHP files are processed (usually automatic in Plesk)

### **🔹 Issue 5: File Permissions**
**Solution:** Set 644 for PHP files, 755 for directories

---

## 🚀 **FINAL CHECKLIST FOR MUWASCO:**

1. ✅ **Update config.php** with exact Plesk credentials
2. ✅ **Upload debug.php** and run it
3. ✅ **Import hr_management.sql** via phpMyAdmin
4. ✅ **Test database connection** in debug output
5. ✅ **Verify all files uploaded** to `/httpdocs/HR/`
6. ✅ **Test login:** admin@company.com / admin123
7. ✅ **Delete debug.php** when working

---

## 🎯 **EXPECTED RESULT:**

After updating config.php with your Plesk database credentials, your HR system should work perfectly with:

- ✅ MariaDB 10.11.5 database
- ✅ nginx web server  
- ✅ PHP 8.4.7 with all extensions
- ✅ 3 default users ready for login

**The main issue is likely just updating the database credentials in config.php!** 🚀