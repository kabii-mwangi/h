# 🚨 PLESK SERVER 500 ERROR - DEBUGGING GUIDE

## 🎯 **QUICK DIAGNOSIS STEPS FOR YOUR PLESK SERVER**

Since you're getting a **500 Internal Server Error** on `https://muwasco.co.ke/HR/login.php` using **Plesk**, let's diagnose and fix this step by step.

---

## 🔍 **STEP 1: CHECK ERROR LOGS IN PLESK (MOST IMPORTANT)**

### **Option A: Via Plesk Panel**
1. **Login to your Plesk Control Panel**
2. **Go to "Websites & Domains"**
3. **Click on your domain** (muwasco.co.ke)
4. **Click "Logs"** in the left sidebar
5. **Select "Error Logs"**
6. **Look for recent entries** (around the time you got the 500 error)

### **Option B: Via File Manager**
1. **In Plesk, go to "Files"**
2. **Navigate to:** `/httpdocs/HR/` or `/public_html/HR/`
3. **Look for these log files:**
   - `error_log`
   - `php_errors.log`
   - Check `/logs/` folder if it exists

### **Option C: PHP Error Logs**
1. **In Plesk, go to "PHP Settings"**
2. **Check if "Log errors" is enabled**
3. **Note the error log path**

---

## 🔧 **STEP 2: ENABLE PHP ERROR DISPLAY (TEMPORARILY)**

### **Method A: Via Plesk PHP Settings**
1. **Go to Websites & Domains**
2. **Click your domain**
3. **Click "PHP Settings"**
4. **Set these values:**
   - `display_errors` = **On**
   - `error_reporting` = **E_ALL**
   - `log_errors` = **On**
5. **Click "Apply"**

### **Method B: Create Debug File**
Create a file called `debug.php` in your HR directory:

```php
<?php
// TEMPORARY DEBUG FILE - DELETE AFTER FIXING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>🔧 PLESK SERVER DEBUG INFO</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current directory: " . getcwd() . "<br>";
echo "Script path: " . __FILE__ . "<br><br>";

echo "<h3>📁 Files in HR directory:</h3>";
$files = scandir('.');
foreach($files as $file) {
    if($file != '.' && $file != '..') {
        echo "📄 " . $file . " - " . (is_readable($file) ? "✅ Readable" : "❌ Not readable") . "<br>";
    }
}

echo "<h3>🗄️ Database Connection Test:</h3>";
if(file_exists('config.php')) {
    echo "✅ config.php found<br>";
    try {
        include 'config.php';
        if(isset($conn) && $conn->ping()) {
            echo "✅ Database connection: SUCCESS<br>";
            echo "✅ Connected to database: " . $database . "<br>";
        } else {
            echo "❌ Database connection: FAILED<br>";
            if(isset($conn)) {
                echo "Error: " . $conn->connect_error . "<br>";
            }
        }
    } catch(Exception $e) {
        echo "❌ Error loading config: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ config.php NOT FOUND<br>";
}

echo "<h3>🔧 PHP Extensions:</h3>";
$required_extensions = ['mysqli', 'json', 'mbstring', 'curl'];
foreach($required_extensions as $ext) {
    echo $ext . ": " . (extension_loaded($ext) ? "✅ Loaded" : "❌ Missing") . "<br>";
}
?>
```

**Then visit:** `https://muwasco.co.ke/HR/debug.php`

---

## 🎯 **STEP 3: PLESK-SPECIFIC COMMON CAUSES & FIXES**

### **🔹 CAUSE 1: Database Configuration for Plesk**
**Symptoms:** Database connection failed
**Fix:** In Plesk, database credentials are usually:

```php
// Your config.php should look like this for Plesk:
$host = 'localhost';  // Or specific DB hostname from Plesk
$username = 'your_plesk_db_user';
$password = 'your_plesk_db_password'; 
$database = 'your_plesk_db_name';
```

**To find correct credentials:**
1. **Go to Plesk > Websites & Domains**
2. **Click "Databases"**
3. **Note your database name and user**
4. **Click on database user to see/reset password**

### **🔹 CAUSE 2: PHP Version in Plesk**
**Symptoms:** Functions not supported
**Fix:**
1. **Go to Websites & Domains**
2. **Click "PHP Settings"**
3. **Select PHP version:** 7.4 or 8.0 (recommended)
4. **Click "Apply"**

### **🔹 CAUSE 3: File Permissions in Plesk**
**Symptoms:** Permission denied errors
**Fix:**
1. **Go to "Files" in Plesk**
2. **Navigate to your HR directory**
3. **Select files/folders**
4. **Click "Change Permissions"**
5. **Set:**
   - **Folders:** 755
   - **PHP files:** 644
   - **config.php:** 644

### **🔹 CAUSE 4: Plesk PHP Extensions**
**Symptoms:** Missing function errors
**Fix:**
1. **Go to Websites & Domains**
2. **Click "PHP Settings"**
3. **Ensure these extensions are enabled:**
   - ✅ mysqli
   - ✅ json
   - ✅ mbstring
   - ✅ curl
   - ✅ openssl

### **🔹 CAUSE 5: Document Root Issues**
**Symptoms:** Files not found
**Fix:** Ensure files are in correct Plesk directory:
- **Usually:** `/httpdocs/HR/`
- **Sometimes:** `/public_html/HR/`

### **🔹 CAUSE 6: Plesk .htaccess Issues**
**Symptoms:** Rewrite errors
**Fix:** 
1. **Check if .htaccess exists**
2. **Temporarily rename to .htaccess-backup**
3. **Test the site**

---

## 🎯 **STEP 4: PLESK DATABASE VERIFICATION**

### **Check Database in Plesk:**
1. **Go to Websites & Domains**
2. **Click "Databases"**
3. **Verify:**
   - ✅ Database exists
   - ✅ Database user exists
   - ✅ User has ALL privileges
   - ✅ Database is accessible

### **Test Database Connection:**
1. **Click "phpMyAdmin" or "Adminer"**
2. **Login with your database credentials**
3. **If login fails = wrong credentials**
4. **If login works = update config.php**

---

## 🚨 **STEP 5: MOST LIKELY ISSUES FOR PLESK**

### **🎯 Issue 1: Plesk Database Naming**
Your local config:
```php
$database = 'hr_management_fresh';
$username = 'hruser';
$password = 'password';
```

**Plesk typically uses:**
```php
$database = 'muwasco_hrmanagement';  // Domain prefix + db name
$username = 'muwasco_hruser';        // Domain prefix + user
$password = 'your_plesk_db_password';
```

### **🎯 Issue 2: File Location**
- Ensure files are in `/httpdocs/HR/` not `/public_html/HR/`
- Plesk uses `httpdocs` as document root

### **🎯 Issue 3: PHP Configuration**
- Check PHP version compatibility
- Ensure required extensions are enabled

---

## ⚡ **QUICK PLESK FIX CHECKLIST:**

1. ✅ **Check Plesk error logs** (Websites & Domains > Logs)
2. ✅ **Upload and run debug.php**
3. ✅ **Verify database credentials** in Plesk Databases section
4. ✅ **Check file location** (should be in `/httpdocs/HR/`)
5. ✅ **Verify PHP version** (PHP Settings)
6. ✅ **Check file permissions** (Files section)
7. ✅ **Enable required PHP extensions**

---

## 📞 **NEED IMMEDIATE HELP?**

**Contact your Plesk hosting provider** with:
- "Getting 500 error on PHP application in Plesk"
- "Please check error logs for muwasco.co.ke/HR/"
- "Need to verify database connection and PHP configuration"

---

## 🎯 **AFTER FIXING:**

1. **Delete debug.php** file (security risk)
2. **Turn off error display** in PHP Settings
3. **Test all login credentials**
4. **Verify all functionality works**

---

## 🚀 **MOST COMMON PLESK SOLUTION:**

**90% of Plesk 500 errors are caused by:**
1. **Wrong database credentials** (check Plesk Databases section)
2. **Files in wrong directory** (use `/httpdocs/` not `/public_html/`)
3. **PHP version mismatch** (set to 7.4 or 8.0 in PHP Settings)

**Start with the error logs and debug.php - they'll show you exactly what's wrong!** 🎯