<?php
require_once 'config.php';

$userId = requireLogin();
$user = getCurrentUser();

// Only super admin can access this page
if (!hasPermission('super_admin')) {
    header('Location: dashboard.php');
    exit();
}

$pdo = getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add_user') {
            $first_name = sanitizeInput($_POST['first_name']);
            $last_name = sanitizeInput($_POST['last_name']);
            $email = sanitizeInput($_POST['email']);
            $password = $_POST['password'];
            $role = $_POST['role'];
            $phone = sanitizeInput($_POST['phone']);
            $address = sanitizeInput($_POST['address']);
            
            try {
                // Check if email already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Email already exists in the system.';
                } else {
                    // Generate unique user ID based on role
                    $rolePrefix = substr($role, 0, 3);
                    $timestamp = time();
                    $userId = $rolePrefix . '-' . $timestamp;
                    
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (id, first_name, last_name, email, password, role, phone, address, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    $stmt->execute([$userId, $first_name, $last_name, $email, $hashedPassword, $role, $phone, $address]);
                    redirectWithMessage('users.php', 'User created successfully!', 'success');
                }
            } catch (PDOException $e) {
                $error = 'Error creating user: ' . $e->getMessage();
            }
        } elseif ($action === 'edit_user') {
            $id = $_POST['id'];
            $first_name = sanitizeInput($_POST['first_name']);
            $last_name = sanitizeInput($_POST['last_name']);
            $email = sanitizeInput($_POST['email']);
            $role = $_POST['role'];
            $phone = sanitizeInput($_POST['phone']);
            $address = sanitizeInput($_POST['address']);
            $password = $_POST['password'];
            
            try {
                // Check if email exists for other users
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $id]);
                if ($stmt->fetch()) {
                    $error = 'Email already exists for another user.';
                } else {
                    if (!empty($password)) {
                        // Update with password
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE users SET first_name=?, last_name=?, email=?, password=?, role=?, phone=?, address=?, updated_at=NOW() WHERE id=?");
                        $stmt->execute([$first_name, $last_name, $email, $hashedPassword, $role, $phone, $address, $id]);
                    } else {
                        // Update without password
                        $stmt = $pdo->prepare("UPDATE users SET first_name=?, last_name=?, email=?, role=?, phone=?, address=?, updated_at=NOW() WHERE id=?");
                        $stmt->execute([$first_name, $last_name, $email, $role, $phone, $address, $id]);
                    }
                    redirectWithMessage('users.php', 'User updated successfully!', 'success');
                }
            } catch (PDOException $e) {
                $error = 'Error updating user: ' . $e->getMessage();
            }
        } elseif ($action === 'delete_user') {
            $id = $_POST['id'];
            
            // Prevent deleting own account
            if ($id == $userId) {
                $error = 'You cannot delete your own account.';
            } else {
                try {
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$id]);
                    redirectWithMessage('users.php', 'User deleted successfully!', 'success');
                } catch (PDOException $e) {
                    $error = 'Error deleting user: ' . $e->getMessage();
                }
            }
        }
    }
}

// Get all users
$users = $pdo->query("SELECT * FROM users ORDER BY first_name, last_name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - HR Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="main-content">
            <div class="header">
                <h1>User Management</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                    <span class="badge badge-info"><?php echo ucwords(str_replace('_', ' ', $user['role'])); ?></span>
                    <a href="logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </div>
            
            <div class="nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="employees.php">Employees</a></li>
                    <li><a href="departments.php">Departments</a></li>
                    <li><a href="users.php" class="active">Users</a></li>
                    <li><a href="reports.php">Reports</a></li>
                </ul>
            </div>
            
            <div class="content">
                <?php $flash = getFlashMessage(); if ($flash): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>">
                        <?php echo htmlspecialchars($flash['message']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>System Users (<?php echo count($users); ?>)</h2>
                    <button onclick="showAddUserModal()" class="btn btn-success">Add New User</button>
                </div>
                
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No users found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user_row): ?>
                                <tr>
                                    <td><?php echo $user_row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user_row['first_name'] . ' ' . $user_row['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user_row['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo getRoleBadge($user_row['role']); ?>">
                                            <?php echo ucwords(str_replace('_', ' ', $user_row['role'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($user_row['phone'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge badge-success">Active</span>
                                    </td>
                                    <td><?php echo formatDate($user_row['created_at']); ?></td>
                                    <td>
                                        <button onclick="showEditUserModal(<?php echo htmlspecialchars(json_encode($user_row)); ?>)" class="btn btn-sm btn-primary">Edit</button>
                                        <?php if ($user_row['id'] != $userId): ?>
                                            <button onclick="confirmDeleteUser('<?php echo $user_row['id']; ?>', '<?php echo htmlspecialchars($user_row['first_name'] . ' ' . $user_row['last_name']); ?>')" class="btn btn-sm btn-danger ml-1">Delete</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New User</h3>
                <span class="close" onclick="hideAddUserModal()">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add_user">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="hr_manager">HR Manager</option>
                            <option value="dept_head">Department Head</option>
                            <option value="section_head">Section Head</option>
                            <option value="manager">Manager</option>
                            <option value="employee">Employee</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Create User</button>
                    <button type="button" class="btn btn-secondary" onclick="hideAddUserModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit User</h3>
                <span class="close" onclick="hideEditUserModal()">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" id="edit_user_id" name="id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_first_name">First Name</label>
                        <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_last_name">Last Name</label>
                        <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_email">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_password">New Password</label>
                        <input type="password" class="form-control" id="edit_password" name="password" placeholder="Leave blank to keep current password">
                        <small class="form-text text-muted">Leave blank to keep current password</small>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_role">Role</label>
                        <select class="form-control" id="edit_role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="hr_manager">HR Manager</option>
                            <option value="dept_head">Department Head</option>
                            <option value="section_head">Section Head</option>
                            <option value="manager">Manager</option>
                            <option value="employee">Employee</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_phone">Phone</label>
                        <input type="text" class="form-control" id="edit_phone" name="phone">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_address">Address</label>
                    <textarea class="form-control" id="edit_address" name="address" rows="3"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <button type="button" class="btn btn-secondary" onclick="hideEditUserModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddUserModal() {
            document.getElementById('addUserModal').style.display = 'block';
        }
        
        function hideAddUserModal() {
            document.getElementById('addUserModal').style.display = 'none';
        }
        
        function showEditUserModal(user) {
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_first_name').value = user.first_name;
            document.getElementById('edit_last_name').value = user.last_name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_role').value = user.role;
            document.getElementById('edit_phone').value = user.phone || '';
            document.getElementById('edit_address').value = user.address || '';
            document.getElementById('edit_password').value = '';
            document.getElementById('editUserModal').style.display = 'block';
        }
        
        function hideEditUserModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }
        
        function confirmDeleteUser(id, name) {
            if (confirm('Are you sure you want to delete user "' + name + '"?\n\nThis action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = '<input type="hidden" name="action" value="delete_user"><input type="hidden" name="id" value="' + id + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            const modals = ['addUserModal', 'editUserModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        }
    </script>

    <style>
        .ml-1 {
            margin-left: 5px;
        }
        .form-text {
            font-size: 0.875em;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
        }
    </style>
</body>
</html>

<?php
function getRoleBadge($role) {
    switch($role) {
        case 'super_admin': return 'badge-danger';
        case 'hr_manager': return 'badge-warning';
        case 'dept_head': return 'badge-info';
        case 'section_head': return 'badge-secondary';
        case 'manager': return 'badge-primary';
        default: return 'badge-light';
    }
}
?>