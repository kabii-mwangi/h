<?php
require_once 'config.php';

$userId = requireLogin();
$user = getCurrentUser();

// Get dashboard statistics
$pdo = getConnection();

// Total employees
$stmt = $pdo->query("SELECT COUNT(*) as count FROM employees WHERE employee_status = 'active'");
$totalEmployees = $stmt->fetch()['count'];

// Total departments
$stmt = $pdo->query("SELECT COUNT(*) as count FROM departments");
$totalDepartments = $stmt->fetch()['count'];

// Total sections
$stmt = $pdo->query("SELECT COUNT(*) as count FROM sections");
$totalSections = $stmt->fetch()['count'];

// Recent employees (last 30 days) - PostgreSQL syntax
$stmt = $pdo->query("SELECT COUNT(*) as count FROM employees WHERE hire_date >= (NOW() - INTERVAL '30 days')");
$recentHires = $stmt->fetch()['count'];

// Get recent employees for display
$stmt = $pdo->query("
    SELECT e.*, 
           COALESCE(e.first_name, SPLIT_PART(e.full_name, ' ', 1)) as first_name,
           COALESCE(e.last_name, SPLIT_PART(e.full_name, ' ', 2)) as last_name,
           d.name as department_name, 
           s.name as section_name 
    FROM employees e 
    LEFT JOIN departments d ON e.department_id = d.id 
    LEFT JOIN sections s ON e.section_id = s.id 
    ORDER BY e.created_at DESC 
    LIMIT 5
");
$recentEmployees = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HR Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="main-content">
            <div class="header">
                <h1>HR Management Dashboard</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                    <span class="badge badge-info"><?php echo ucwords(str_replace('_', ' ', $user['role'])); ?></span>
                    <a href="logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </div>
            
            <div class="nav">
                <ul>
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="employees.php">Employees</a></li>
                    <?php if (hasPermission('hr_manager')): ?>
                    <li><a href="departments.php">Departments</a></li>
                    <?php endif; ?>
                    <?php if (hasPermission('super_admin')): ?>
                    <li><a href="users.php">Users</a></li>
                    <?php endif; ?>
                    <?php if (hasPermission('hr_manager')): ?>
                    <li><a href="reports.php">Reports</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="content">
                <?php $flash = getFlashMessage(); if ($flash): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>">
                        <?php echo htmlspecialchars($flash['message']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3><?php echo $totalEmployees; ?></h3>
                        <p>Active Employees</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $totalDepartments; ?></h3>
                        <p>Departments</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $totalSections; ?></h3>
                        <p>Sections</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $recentHires; ?></h3>
                        <p>New Hires (30 days)</p>
                    </div>
                </div>
                
                <div class="table-container">
                    <h3 style="padding: 20px 20px 0; margin: 0;">Recent Employees</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Section</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Hire Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentEmployees)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No employees found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentEmployees as $employee): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($employee['employee_id']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['department_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($employee['section_name'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge <?php echo getEmployeeTypeBadge($employee['employee_type']); ?>">
                                            <?php echo ucwords(str_replace('_', ' ', $employee['employee_type'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo getEmployeeStatusBadge($employee['employee_status']); ?>">
                                            <?php echo ucwords($employee['employee_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatDate($employee['hire_date']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 30px; text-align: center;">
                    <a href="employees.php" class="btn btn-primary">View All Employees</a>
                    <?php if (hasPermission('hr_manager')): ?>
                        <a href="employees.php?action=add" class="btn btn-success">Add New Employee</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>