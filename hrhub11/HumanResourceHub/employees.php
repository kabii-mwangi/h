<?php
require_once 'config.php';

$userId = requireLogin();
$user = getCurrentUser();
$pdo = getConnection();

// Handle form submission for adding/editing employees
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add' && hasPermission('hr_manager')) {
            $employee_id = sanitizeInput($_POST['employee_id']);
            $first_name = sanitizeInput($_POST['first_name']);
            $last_name = sanitizeInput($_POST['last_name']);
            $national_id = sanitizeInput($_POST['national_id']);
            $email = sanitizeInput($_POST['email']);
            $phone = sanitizeInput($_POST['phone']);
            $address = sanitizeInput($_POST['address']);
            $date_of_birth = $_POST['date_of_birth'];
            $hire_date = $_POST['hire_date'];
            $designation = sanitizeInput($_POST['designation']) ?: 'Employee';
            $department_id = $_POST['department_id'];
            $section_id = $_POST['section_id'];
            $employee_type = $_POST['employee_type'];
            $employment_type = $_POST['employment_type'] ?: 'permanent';

            
            try {
                $full_name = trim($first_name . ' ' . $last_name);
                $stmt = $pdo->prepare("INSERT INTO employees (employee_id, full_name, national_id, phone_number, email, date_of_birth, designation, department_id, section_id, employee_type, employment_type, employee_status, first_name, last_name, address, hire_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, ?, ?, ?)");
                $stmt->execute([$employee_id, $full_name, $national_id, $phone, $email, $date_of_birth, $designation, $department_id, $section_id, $employee_type, $employment_type, $first_name, $last_name, $address, $hire_date]);
                redirectWithMessage('employees.php', 'Employee added successfully!', 'success');
            } catch (PDOException $e) {
                $error = 'Error adding employee: ' . $e->getMessage();
            }
        } elseif ($action === 'edit' && hasPermission('hr_manager')) {
            $id = $_POST['id'];
            $employee_id = sanitizeInput($_POST['employee_id']);
            $first_name = sanitizeInput($_POST['first_name']);
            $last_name = sanitizeInput($_POST['last_name']);
            $national_id = sanitizeInput($_POST['national_id']);
            $email = sanitizeInput($_POST['email']);
            $phone = sanitizeInput($_POST['phone']);
            $address = sanitizeInput($_POST['address']);
            $date_of_birth = $_POST['date_of_birth'];
            $hire_date = $_POST['hire_date'];
            $designation = sanitizeInput($_POST['designation']);
            $department_id = $_POST['department_id'];
            $section_id = $_POST['section_id'];
            $employee_type = $_POST['employee_type'];
            $employment_type = $_POST['employment_type'];
            $employee_status = $_POST['employee_status'];
            
            try {
                $full_name = trim($first_name . ' ' . $last_name);
                $stmt = $pdo->prepare("UPDATE employees SET employee_id=?, full_name=?, first_name=?, last_name=?, national_id=?, email=?, phone_number=?, address=?, date_of_birth=?, hire_date=?, designation=?, department_id=?, section_id=?, employee_type=?, employment_type=?, employee_status=?, updated_at=NOW() WHERE id=?");
                $stmt->execute([$employee_id, $full_name, $first_name, $last_name, $national_id, $email, $phone, $address, $date_of_birth, $hire_date, $designation, $department_id, $section_id, $employee_type, $employment_type, $employee_status, $id]);
                redirectWithMessage('employees.php', 'Employee updated successfully!', 'success');
            } catch (PDOException $e) {
                $error = 'Error updating employee: ' . $e->getMessage();
            }
        }
    }
}

// Get filter parameters
$search = $_GET['search'] ?? '';
$department_filter = $_GET['department'] ?? '';
$section_filter = $_GET['section'] ?? '';
$type_filter = $_GET['type'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Build query with filters
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(e.first_name LIKE ? OR e.last_name LIKE ? OR e.employee_id LIKE ? OR e.email LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

if (!empty($department_filter)) {
    $where_conditions[] = "e.department_id = ?";
    $params[] = $department_filter;
}

if (!empty($section_filter)) {
    $where_conditions[] = "e.section_id = ?";
    $params[] = $section_filter;
}

if (!empty($type_filter)) {
    $where_conditions[] = "e.employee_type = ?";
    $params[] = $type_filter;
}

if (!empty($status_filter)) {
    $where_conditions[] = "e.employee_status = ?";
    $params[] = $status_filter;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$query = "
    SELECT e.*, 
           COALESCE(e.first_name, SPLIT_PART(e.full_name, ' ', 1)) as first_name,
           COALESCE(e.last_name, SPLIT_PART(e.full_name, ' ', 2)) as last_name,
           d.name as department_name, 
           s.name as section_name 
    FROM employees e 
    LEFT JOIN departments d ON e.department_id = d.id 
    LEFT JOIN sections s ON e.section_id = s.id 
    $where_clause
    ORDER BY e.created_at DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$employees = $stmt->fetchAll();

// Get departments and sections for filters and forms
$departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll();
$sections = $pdo->query("SELECT s.*, d.name as department_name FROM sections s LEFT JOIN departments d ON s.department_id = d.id ORDER BY d.name, s.name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees - HR Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="main-content">
            <div class="header">
                <h1>Employee Management</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                    <span class="badge badge-info"><?php echo ucwords(str_replace('_', ' ', $user['role'])); ?></span>
                    <a href="logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </div>
            
            <div class="nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="employees.php" class="active">Employees</a></li>
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
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Employees (<?php echo count($employees); ?>)</h2>
                    <?php if (hasPermission('hr_manager')): ?>
                        <button onclick="showAddModal()" class="btn btn-success">Add New Employee</button>
                    <?php endif; ?>
                </div>
                
                <!-- Search and Filters -->
                <div class="search-filters">
                    <form method="GET" action="">
                        <div class="filter-row">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="Name, ID, or Email">
                            </div>
                            <div class="form-group">
                                <label for="department">Department</label>
                                <select class="form-control" id="department" name="department">
                                    <option value="">All Departments</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo $dept['id']; ?>" 
                                                <?php echo $department_filter == $dept['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dept['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="type">Employee Type</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="">All Types</option>
                                    <option value="officer" <?php echo $type_filter === 'officer' ? 'selected' : ''; ?>>Officer</option>
                                    <option value="section_head" <?php echo $type_filter === 'section_head' ? 'selected' : ''; ?>>Section Head</option>
                                    <option value="manager" <?php echo $type_filter === 'manager' ? 'selected' : ''; ?>>Manager</option>
                                    <option value="dept_head" <?php echo $type_filter === 'dept_head' ? 'selected' : ''; ?>>Department Head</option>
                                    <option value="managing_director" <?php echo $type_filter === 'managing_director' ? 'selected' : ''; ?>>Managing Director</option>
                                    <option value="bod_chairman" <?php echo $type_filter === 'bod_chairman' ? 'selected' : ''; ?>>BOD Chairman</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="terminated" <?php echo $status_filter === 'terminated' ? 'selected' : ''; ?>>Terminated</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="employees.php" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Employees Table -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Section</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($employees)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No employees found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($employee['employee_id']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['email'] ?? 'N/A'); ?></td>
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
                                    <td>
                                        <?php if (hasPermission('hr_manager')): ?>
                                            <button onclick="showEditModal(<?php echo htmlspecialchars(json_encode($employee)); ?>)" class="btn btn-sm btn-primary">Edit</button>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
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

    <!-- Add Employee Modal -->
    <?php if (hasPermission('hr_manager')): ?>
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Employee</h3>
                <span class="close" onclick="hideAddModal()">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="employee_id">Employee ID</label>
                        <input type="text" class="form-control" id="employee_id" name="employee_id" required>
                    </div>
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="national_id">National ID</label>
                        <input type="text" class="form-control" id="national_id" name="national_id" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="designation">Designation</label>
                        <input type="text" class="form-control" id="designation" name="designation" required placeholder="e.g. Software Engineer">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="hire_date">Hire Date</label>
                        <input type="date" class="form-control" id="hire_date" name="hire_date" required>
                    </div>
                    <div class="form-group">
                        <label for="employment_type">Employment Type</label>
                        <select class="form-control" id="employment_type" name="employment_type" required>
                            <option value="">Select Type</option>
                            <option value="permanent">Permanent</option>
                            <option value="contract">Contract</option>
                            <option value="temporary">Temporary</option>
                            <option value="intern">Intern</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="employee_type">Employee Type</label>
                        <select class="form-control" id="employee_type" name="employee_type" required onchange="handleEmployeeTypeChange()">
                            <option value="">Select Type</option>
                            <option value="officer">Officer</option>
                            <option value="section_head">Section Head</option>
                            <option value="manager">Manager</option>
                            <option value="dept_head">Department Head</option>
                            <option value="managing_director">Managing Director</option>
                            <option value="bod_chairman">BOD Chairman</option>
                        </select>
                    </div>
                    <div class="form-group" id="department_group">
                        <label for="department_id">Department</label>
                        <select class="form-control" id="department_id" name="department_id" onchange="updateSections()">
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group" id="section_group">
                    <label for="section_id">Section</label>
                    <select class="form-control" id="section_id" name="section_id">
                        <option value="">Select Section</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Add Employee</button>
                    <button type="button" class="btn btn-secondary" onclick="hideAddModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Edit Employee Modal -->
    <?php if (hasPermission('hr_manager')): ?>
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Employee</h3>
                <span class="close" onclick="hideEditModal()">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_id" name="id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_employee_id">Employee ID</label>
                        <input type="text" class="form-control" id="edit_employee_id" name="employee_id" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_first_name">First Name</label>
                        <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_last_name">Last Name</label>
                        <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_national_id">National ID</label>
                        <input type="text" class="form-control" id="edit_national_id" name="national_id" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_email">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_designation">Designation</label>
                        <input type="text" class="form-control" id="edit_designation" name="designation" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_phone">Phone</label>
                        <input type="text" class="form-control" id="edit_phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_date_of_birth">Date of Birth</label>
                        <input type="date" class="form-control" id="edit_date_of_birth" name="date_of_birth" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_address">Address</label>
                    <textarea class="form-control" id="edit_address" name="address" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_hire_date">Hire Date</label>
                        <input type="date" class="form-control" id="edit_hire_date" name="hire_date" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_employment_type">Employment Type</label>
                        <select class="form-control" id="edit_employment_type" name="employment_type" required>
                            <option value="">Select Type</option>
                            <option value="permanent">Permanent</option>
                            <option value="contract">Contract</option>
                            <option value="temporary">Temporary</option>
                            <option value="intern">Intern</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_employee_type">Employee Type</label>
                        <select class="form-control" id="edit_employee_type" name="employee_type" required onchange="handleEditEmployeeTypeChange()">
                            <option value="">Select Type</option>
                            <option value="officer">Officer</option>
                            <option value="section_head">Section Head</option>
                            <option value="manager">Manager</option>
                            <option value="dept_head">Department Head</option>
                            <option value="managing_director">Managing Director</option>
                            <option value="bod_chairman">BOD Chairman</option>
                        </select>
                    </div>
                    <div class="form-group" id="edit_department_group">
                        <label for="edit_department_id">Department</label>
                        <select class="form-control" id="edit_department_id" name="department_id" onchange="updateEditSections()">
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group" id="edit_section_group">
                        <label for="edit_section_id">Section</label>
                        <select class="form-control" id="edit_section_id" name="section_id">
                            <option value="">Select Section</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_employee_status">Status</label>
                        <select class="form-control" id="edit_employee_status" name="employee_status" required>
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Employee</button>
                    <button type="button" class="btn btn-secondary" onclick="hideEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        function showAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }
        
        function hideAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }
        
        function showEditModal(employee) {
            document.getElementById('edit_id').value = employee.id;
            document.getElementById('edit_employee_id').value = employee.employee_id;
            document.getElementById('edit_first_name').value = employee.first_name || '';
            document.getElementById('edit_last_name').value = employee.last_name || '';
            document.getElementById('edit_national_id').value = employee.national_id;
            document.getElementById('edit_email').value = employee.email;
            document.getElementById('edit_designation').value = employee.designation;
            document.getElementById('edit_phone').value = employee.phone_number;
            document.getElementById('edit_date_of_birth').value = employee.date_of_birth;
            document.getElementById('edit_hire_date').value = employee.hire_date;
            document.getElementById('edit_address').value = employee.address || '';
            document.getElementById('edit_employment_type').value = employee.employment_type;
            document.getElementById('edit_employee_type').value = employee.employee_type;
            document.getElementById('edit_department_id').value = employee.department_id;
            document.getElementById('edit_employee_status').value = employee.employee_status;
            
            // Update sections for selected department
            updateEditSections();
            setTimeout(() => {
                document.getElementById('edit_section_id').value = employee.section_id;
            }, 100);
            
            // Handle employee type visibility
            handleEditEmployeeTypeChange();
            
            document.getElementById('editModal').style.display = 'block';
        }
        
        function hideEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        function handleEmployeeTypeChange() {
            const employeeType = document.getElementById('employee_type').value;
            const departmentGroup = document.getElementById('department_group');
            const sectionGroup = document.getElementById('section_group');
            
            if (employeeType === 'managing_director' || employeeType === 'bod_chairman') {
                departmentGroup.style.display = 'none';
                sectionGroup.style.display = 'none';
                document.getElementById('department_id').value = '';
                document.getElementById('section_id').value = '';
            } else if (employeeType === 'dept_head') {
                departmentGroup.style.display = 'block';
                sectionGroup.style.display = 'none';
                document.getElementById('section_id').value = '';
            } else {
                departmentGroup.style.display = 'block';
                sectionGroup.style.display = 'block';
            }
        }
        
        function updateSections() {
            const departmentId = document.getElementById('department_id').value;
            const sectionSelect = document.getElementById('section_id');
            
            // Clear existing options
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            
            if (departmentId) {
                // Add sections for selected department
                const sections = <?php echo json_encode($sections); ?>;
                sections.forEach(function(section) {
                    if (section.department_id == departmentId) {
                        const option = document.createElement('option');
                        option.value = section.id;
                        option.textContent = section.name;
                        sectionSelect.appendChild(option);
                    }
                });
            }
        }
        
        function handleEditEmployeeTypeChange() {
            const employeeType = document.getElementById('edit_employee_type').value;
            const departmentGroup = document.getElementById('edit_department_group');
            const sectionGroup = document.getElementById('edit_section_group');
            
            if (employeeType === 'managing_director' || employeeType === 'bod_chairman') {
                departmentGroup.style.display = 'none';
                sectionGroup.style.display = 'none';
                document.getElementById('edit_department_id').value = '';
                document.getElementById('edit_section_id').value = '';
            } else if (employeeType === 'dept_head') {
                departmentGroup.style.display = 'block';
                sectionGroup.style.display = 'none';
                document.getElementById('edit_section_id').value = '';
            } else {
                departmentGroup.style.display = 'block';
                sectionGroup.style.display = 'block';
            }
        }
        
        function updateEditSections() {
            const departmentId = document.getElementById('edit_department_id').value;
            const sectionSelect = document.getElementById('edit_section_id');
            
            // Clear existing options
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            
            if (departmentId) {
                // Add sections for selected department
                const sections = <?php echo json_encode($sections); ?>;
                sections.forEach(function(section) {
                    if (section.department_id == departmentId) {
                        const option = document.createElement('option');
                        option.value = section.id;
                        option.textContent = section.name;
                        sectionSelect.appendChild(option);
                    }
                });
            }
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            if (event.target == addModal) {
                hideAddModal();
            } else if (event.target == editModal) {
                hideEditModal();
            }
        }
    </script>
</body>
</html>