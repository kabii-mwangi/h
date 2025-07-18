<?php
// LOGIN CREDENTIALS DEBUG - Check why default logins aren't working
require_once 'config.php';

echo "<h2>🔍 LOGIN CREDENTIALS DEBUG</h2>";

// Test 1: Check if users table exists and has data
echo "<h3>1. Users Table Check:</h3>";
try {
    $result = $conn->query("SELECT id, email, first_name, last_name, role, password FROM users");
    
    if ($result && $result->num_rows > 0) {
        echo "✅ Users table found with " . $result->num_rows . " users<br><br>";
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Email</th><th>Name</th><th>Role</th><th>Password (First 20 chars)</th></tr>";
        
        while ($user = $result->fetch_assoc()) {
            $passwordPreview = substr($user['password'], 0, 20) . "...";
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "<td>" . htmlspecialchars($passwordPreview) . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
    } else {
        echo "❌ No users found in database<br>";
        echo "• This means you need to import your SQL file<br><br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking users: " . $e->getMessage() . "<br><br>";
}

// Test 2: Test specific default credentials
echo "<h3>2. Default Credentials Test:</h3>";
$default_credentials = [
    ['email' => 'admin@company.com', 'password' => 'admin123'],
    ['email' => 'hr@company.com', 'password' => 'hr123'],
    ['email' => 'depthead@company.com', 'password' => 'dept123']
];

foreach ($default_credentials as $cred) {
    echo "<strong>Testing: " . $cred['email'] . " / " . $cred['password'] . "</strong><br>";
    
    $email = $conn->real_escape_string($cred['email']);
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "• User found in database ✅<br>";
        echo "• Stored password hash: " . substr($user['password'], 0, 30) . "...<br>";
        
        // Test password verification
        if (password_verify($cred['password'], $user['password'])) {
            echo "• Password verification: ✅ SUCCESS<br>";
            echo "• <strong>This login should work!</strong><br>";
        } else {
            echo "• Password verification: ❌ FAILED<br>";
            echo "• The stored hash doesn't match the expected password<br>";
            
            // Test if it's a plain text password
            if ($user['password'] === $cred['password']) {
                echo "• Appears to be plain text password - SECURITY ISSUE<br>";
            } else {
                echo "• Need to reset this user's password<br>";
            }
        }
    } else {
        echo "• ❌ User NOT found in database<br>";
    }
    echo "<br>";
}

// Test 3: Check password hashing function
echo "<h3>3. Password Hashing Test:</h3>";
$test_password = "admin123";
$hash = password_hash($test_password, PASSWORD_DEFAULT);
echo "• Test password: " . $test_password . "<br>";
echo "• Generated hash: " . $hash . "<br>";
echo "• Verification test: " . (password_verify($test_password, $hash) ? "✅ Works" : "❌ Failed") . "<br><br>";

// Test 4: Create/Fix default users if needed
echo "<h3>4. Fix Default Users:</h3>";
echo "<strong>Click the links below to fix user passwords:</strong><br>";

foreach ($default_credentials as $cred) {
    $email = urlencode($cred['email']);
    $password = urlencode($cred['password']);
    echo "• <a href='?fix_user=" . $email . "&password=" . $password . "'>Fix " . htmlspecialchars($cred['email']) . "</a><br>";
}

// Handle user fixing
if (isset($_GET['fix_user']) && isset($_GET['password'])) {
    $email = $conn->real_escape_string($_GET['fix_user']);
    $password = $_GET['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if user exists
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    
    if ($check && $check->num_rows > 0) {
        // Update existing user
        $update = $conn->query("UPDATE users SET password = '$hash' WHERE email = '$email'");
        if ($update) {
            echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb;'>";
            echo "✅ Updated password for: $email<br>";
            echo "You can now login with: $email / $password";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb;'>";
            echo "❌ Failed to update password for: $email";
            echo "</div>";
        }
    } else {
        // Create new user
        $parts = explode('@', $email);
        $name_part = $parts[0];
        $first_name = ucfirst(str_replace(['admin', 'hr', 'dept'], ['Admin', 'HR', 'Department'], $name_part));
        $last_name = $email === 'depthead@company.com' ? 'Head' : 'User';
        
        $role = 'employee';
        if (strpos($email, 'admin') !== false) $role = 'super_admin';
        elseif (strpos($email, 'hr') !== false) $role = 'hr_manager';
        elseif (strpos($email, 'dept') !== false) $role = 'dept_head';
        
        $id = $role === 'super_admin' ? 'admin-001' : ($role === 'hr_manager' ? 'hr-001' : 'dept-001');
        
        $insert = $conn->query("INSERT INTO users (id, email, first_name, last_name, password, role, created_at, updated_at) VALUES ('$id', '$email', '$first_name', '$last_name', '$hash', '$role', NOW(), NOW())");
        
        if ($insert) {
            echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb;'>";
            echo "✅ Created new user: $email<br>";
            echo "You can now login with: $email / $password";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb;'>";
            echo "❌ Failed to create user: $email<br>";
            echo "Error: " . $conn->error;
            echo "</div>";
        }
    }
}

echo "<h3>🎯 Summary:</h3>";
echo "1. If users are missing → Import your SQL file or use the fix links above<br>";
echo "2. If password verification fails → Use the fix links above<br>";
echo "3. After fixing, try logging in with the default credentials<br>";
echo "4. Delete this debug file when done<br>";
?>