# 🚨 LIVE SERVER 500 ERROR - DEBUGGING GUIDE

## 🎯 **QUICK DIAGNOSIS STEPS FOR YOUR LIVE SERVER**

Since you're getting a **500 Internal Server Error** on `https://muwasco.co.ke/HR/login.php`, let's diagnose and fix this step by step.

---

## 🔍 **STEP 1: CHECK ERROR LOGS (MOST IMPORTANT)**

### **Option A: Via cPanel File Manager**
1. **Login to your cPanel**
2. **Go to File Manager**
3. **Navigate to your HR directory:** `/public_html/HR/`
4. **Look for these log files:**
   - `error_log`
   - `php_errors.log` 
   - Check the `/logs/` folder if it exists

### **Option B: Via cPanel Error Logs**
1. **In cPanel, find "Error Logs"**
2. **Select your domain**
3. **Look for recent entries** (around the time you got the 500 error)

### **Option C: Check Server Error Logs**
- Ask your hosting provider for recent error log entries for your domain

---

## 🔧 **STEP 2: ENABLE PHP ERROR DISPLAY (TEMPORARILY)**

Create a file called `debug.php` in your HR directory with this content:

```php
<?php
// TEMPORARY DEBUG FILE - DELETE AFTER FIXING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Current directory: " . getcwd() . "<br>";
echo "Files in directory:<br>";
print_r(scandir('.'));

// Test database connection
echo "<br><br>Testing database connection...<br>";
include 'config.php';
if ($conn->ping()) {
    echo "✅ Database connection: SUCCESS<br>";
} else {
    echo "❌ Database connection: FAILED - " . $conn->connect_error . "<br>";
}
?>
```

**Then visit:** `https://muwasco.co.ke/HR/debug.php`

---

## 🎯 **STEP 3: COMMON CAUSES & FIXES**

### **🔹 CAUSE 1: Database Connection Issues**
**Symptoms:** Can't connect to database
**Fix:** Update your `config.php` with correct hosting database details:

```php
$host = 'localhost';  // Or your hosting provider's DB host
$username = 'your_cpanel_db_user';
$password = 'your_cpanel_db_password'; 
$database = 'your_cpanel_db_name';
```

### **🔹 CAUSE 2: PHP Version Compatibility**
**Symptoms:** Functions not supported
**Fix:** In cPanel, go to **"Select PHP Version"** and choose PHP 7.4 or 8.0

### **🔹 CAUSE 3: File Permissions**
**Symptoms:** Permission denied errors
**Fix:** Set correct permissions:
- **Folders:** 755 or 755
- **PHP files:** 644
- **config.php:** 644

### **🔹 CAUSE 4: Missing Files**
**Symptoms:** File not found errors  
**Fix:** Ensure all files uploaded correctly - re-upload missing files

### **🔹 CAUSE 5: .htaccess Issues**
**Symptoms:** Rewrite rules causing problems
**Fix:** Temporarily rename `.htaccess` to `.htaccess-backup` and test

### **🔹 CAUSE 6: Memory/Execution Limits**
**Symptoms:** Script timeout or memory exceeded
**Fix:** Create `.htaccess` in HR directory with:

```apache
php_value memory_limit 256M
php_value max_execution_time 300
php_value upload_max_filesize 64M
php_value post_max_size 64M
```

---

## 🎯 **STEP 4: CHECK YOUR HOSTING ENVIRONMENT**

### **Verify Database Setup:**
1. **Login to cPanel**
2. **Go to "MySQL Databases"**
3. **Verify:**
   - Database exists: `your_db_name`
   - User exists and has ALL privileges
   - User is assigned to the database

### **Check PHP Configuration:**
1. **In cPanel, go to "Select PHP Version"**
2. **Enable these extensions:**
   - mysqli
   - json
   - mbstring
   - curl

---

## 🚨 **STEP 5: MOST LIKELY ISSUES FOR YOUR SETUP**

Based on your local working system, these are the most probable causes:

### **🎯 Issue 1: Database Configuration Mismatch**
Your local config uses:
```php
$database = 'hr_management_fresh';
$username = 'hruser';
$password = 'password';
```

**But your hosting likely needs:**
```php
$database = 'username_hrmanagement';  // Usually prefixed with cPanel username
$username = 'username_hruser';        // Usually prefixed with cPanel username  
$password = 'your_hosting_db_password';
```

### **🎯 Issue 2: PHP Version Differences**
- Your local system might use different PHP version
- Check if your hosting supports your PHP requirements

---

## ⚡ **QUICK FIX CHECKLIST:**

1. ✅ **Check error logs** (most important!)
2. ✅ **Upload debug.php** and check output
3. ✅ **Verify database credentials** in config.php
4. ✅ **Check file permissions** (644 for PHP files)
5. ✅ **Ensure PHP version compatibility**
6. ✅ **Verify all files uploaded correctly**

---

## 📞 **NEED IMMEDIATE HELP?**

**Contact your hosting provider** with these details:
- "Getting 500 error on PHP application"
- "Please check error logs for muwasco.co.ke/HR/"
- "Need to verify database connection and PHP configuration"

---

## 🎯 **AFTER FIXING:**

1. **Delete debug.php** file (security risk)
2. **Remove any error display settings** from production
3. **Test all login credentials**
4. **Verify all functionality works**

Most 500 errors are caused by **database connection issues** or **file permission problems**. Start with checking your error logs and database configuration - that usually solves 80% of cases! 🚀