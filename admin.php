<?php
// Database configuration and session start
session_start();

require_once 'db_connect.php';

// Fetch all phones for the phones table
$stmt = $pdo->query("SELECT * FROM phones ORDER BY id DESC");
$phones = $stmt->fetchAll();

// Fetch all staff for the staff table
$stmt = $pdo->query("SELECT * FROM staff ORDER BY id DESC");
$staff = $stmt->fetchAll();

// Get counts for dashboard
$phonesCount = $pdo->query("SELECT COUNT(*) FROM phones")->fetchColumn();
$staffCount = $pdo->query("SELECT COUNT(*) FROM staff")->fetchColumn();
$lowStockCount = $pdo->query("SELECT COUNT(*) FROM phones WHERE stock < 5")->fetchColumn();

try {
    $stmt = $pdo->query("SELECT * FROM activities ORDER BY created_at DESC LIMIT 5");
    $activities = $stmt->fetchAll();
} catch (PDOException $e) {
    // If activities table doesn't exist, ignore
    $activities = [];
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: home.php");
    exit();
}

// Handle phone actions
if (isset($_POST['add_phone'])) {
    $model = $_POST['model'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $specs = $_POST['specs'];
    
    // Handle file upload
    $imagePath = '';
    if (isset($_FILES['phone_image']) && $_FILES['phone_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . basename($_FILES['phone_image']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['phone_image']['tmp_name'], $targetPath)) {
            $imagePath = $targetPath;
        } else {
            $error_message = "Failed to upload image";
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO phones (image_path, model, brand, price, stock, specifications, release_date) 
                          VALUES (?, ?, ?, ?, ?, ?, CURDATE())");
    $stmt->execute([$imagePath, $model, $brand, $price, $stock, $specs]);
    
    $success_message = "Phone added successfully";
    header("Location: ".$_SERVER['PHP_SELF']."?phones");
    exit();
}

if (isset($_POST['delete_phone'])) {
    $phoneId = $_POST['phone_id'];
    
    // First get the image path before deleting
    $stmt = $pdo->prepare("SELECT image_path FROM phones WHERE id = ?");
    $stmt->execute([$phoneId]);
    $phone = $stmt->fetch();
    
    // Delete the phone record
    $stmt = $pdo->prepare("DELETE FROM phones WHERE id = ?");
    $stmt->execute([$phoneId]);
    
    // Delete the image file if it exists
    if (!empty($phone['image_path']) && file_exists($phone['image_path'])) {
        unlink($phone['image_path']);
    }
    
    $success_message = "Phone deleted successfully";
    header("Location: ".$_SERVER['PHP_SELF']."?phones");
    exit();
}

// Handle staff actions
if (isset($_POST['add_staff'])) {
    $nic = $_POST['nic'];
    $name = $_POST['full_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $salary = $_POST['salary'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("SELECT id FROM staff WHERE nic = ? OR email = ?");
    $stmt->execute([$nic, $email]);
    
    if ($stmt->fetch()) {
        $error_message = 'Staff member with this NIC or email already exists';
    } else {
        $stmt = $pdo->prepare("INSERT INTO staff (nic, full_name, address, phone, email, position, salary, password, join_date) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE())");
        $stmt->execute([$nic, $name, $address, $phone, $email, $position, $salary, $password]);
        
        $success_message = 'Staff added successfully';
        header("Location: ".$_SERVER['PHP_SELF']."?staff");
        exit();
    }
}

// Handle staff update
if (isset($_POST['update_staff'])) {
    $staffId = $_POST['staff_id'];
    $nic = $_POST['nic'];
    $name = $_POST['full_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $salary = $_POST['salary'];
    
    // Check if password was provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE staff SET nic=?, full_name=?, address=?, phone=?, email=?, position=?, salary=?, password=? WHERE id=?");
        $stmt->execute([$nic, $name, $address, $phone, $email, $position, $salary, $password, $staffId]);
    } else {
        $stmt = $pdo->prepare("UPDATE staff SET nic=?, full_name=?, address=?, phone=?, email=?, position=?, salary=? WHERE id=?");
        $stmt->execute([$nic, $name, $address, $phone, $email, $position, $salary, $staffId]);
    }
    
    $success_message = 'Staff updated successfully';
    header("Location: ".$_SERVER['PHP_SELF']."?staff");
    exit();
}

if (isset($_POST['delete_staff'])) {
    $staffId = $_POST['staff_id'];
    
    $stmt = $pdo->prepare("DELETE FROM staff WHERE id = ?");
    $stmt->execute([$staffId]);
    
    $success_message = 'Staff deleted successfully';
    header("Location: ".$_SERVER['PHP_SELF']."?staff");
    exit();
}

// Handle admin settings
if (isset($_POST['update_admin'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (!empty($newPassword)) {
        $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();
        
        if (!password_verify($currentPassword, $admin['password'])) {
            $error_message = 'Current password is incorrect';
        } elseif ($newPassword !== $confirmPassword) {
            $error_message = 'New passwords do not match';
        } else {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE admins SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$name, $email, $passwordHash, $_SESSION['admin_id']]);
            
            $_SESSION['admin_name'] = $name;
            $_SESSION['admin_email'] = $email;
            
            $success_message = 'Settings updated successfully';
        }
    } else {
        $stmt = $pdo->prepare("UPDATE admins SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $_SESSION['admin_id']]);
        
        $_SESSION['admin_name'] = $name;
        $_SESSION['admin_email'] = $email;
        
        $success_message = 'Settings updated successfully';
    }
}

// Determine current section
$section = 'dashboard';
if (isset($_GET['phones'])) $section = 'phones';
if (isset($_GET['staff'])) $section = 'staff';
if (isset($_GET['settings'])) $section = 'settings';
if (isset($_GET['login'])) $section = 'login';

// Handle AJAX request for staff details
if (isset($_GET['get_staff_details'])) {
    header('Content-Type: application/json');
    
    if (isset($_GET['staff_id'])) {
        $staffId = $_GET['staff_id'];
        
        $stmt = $pdo->prepare("SELECT * FROM staff WHERE id = ?");
        $stmt->execute([$staffId]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($staff) {
            echo json_encode([
                'success' => true,
                'staff' => $staff
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Staff member not found'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Staff ID not provided'
        ]);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Phone & Staff Management</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        
        .dashboard {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            padding: 15px 20px;
            border-bottom: 1px solid #34495e;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .sidebar-menu li:hover, .sidebar-menu li.active {
            background-color: #34495e;
        }
        
        .sidebar-menu li i {
            margin-right: 10px;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        
        .card {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-success {
            background-color: #2ecc71;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #27ae60;
        }
        
        .btn-warning {
            background-color: #f39c12;
            color: white;
        }
        
        .btn-warning:hover {
            background-color:rgb(210, 94, 17);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 5px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .search-container {
            margin-bottom: 20px;
        }
        
        .search-container input {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
        }
        
        .tab.active {
            border-bottom: 3px solid #3498db;
            font-weight: bold;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Login form */
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 350px;
            margin: 100px auto;
        }
        
        .login-container h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        
        .error {
            color: #e74c3c;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .success {
            color: #2ecc71;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php if ($section === 'login'): ?>
        <div class="login-container">
            <h1>Admin Login</h1>
            
            <?php if (isset($login_error)): ?>
                <div class="error"><?php echo $login_error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required class="form-control">
                </div>
                <button type="submit" name="login" class="btn btn-primary">Login</button>
            </form>
        </div>
    <?php else: ?>
        <div class="dashboard">
            <div class="sidebar">
                <div class="sidebar-header">
                    <h2>Admin Dashboard</h2>
                    <p>Phone & Staff Management</p>
                </div>
                <ul class="sidebar-menu">
                    <li class="<?php echo $section === 'dashboard' ? 'active' : ''; ?>" onclick="window.location='?dashboard'"><i>📊</i> Dashboard</li>
                    <li class="<?php echo $section === 'phones' ? 'active' : ''; ?>" onclick="window.location='?phones'"><i>📱</i> Phone Inventory</li>
                    <li class="<?php echo $section === 'staff' ? 'active' : ''; ?>" onclick="window.location='?staff'"><i>👥</i> Staff Management</li>
                    <li class="<?php echo $section === 'settings' ? 'active' : ''; ?>" onclick="window.location='?settings'"><i>⚙️</i> Admin Settings</li>
                    <li onclick="window.location='?logout'"><i>🚪</i> Logout</li>
                </ul>
            </div>
            
            <div class="main-content">
                <div class="header">
                    <h1 id="sectionTitle">
                        <?php 
                            echo match($section) {
                                'dashboard' => 'Admin Dashboard',
                                'phones' => 'Phone Inventory Management',
                                'staff' => 'Staff Management',
                                'settings' => 'Admin Settings',
                                default => 'Admin Dashboard'
                            };
                        ?>
                    </h1>
                    <div>
                        <span>Welcome, <span id="adminName"><?php echo $_SESSION['admin_name'] ?? 'Admin'; ?></span></span>
                    </div>
                </div>
                
                <?php if (isset($success_message)): ?>
                    <div class="card success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="card error"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div id="dashboardSection" class="tab-content <?php echo $section === 'dashboard' ? 'active' : ''; ?>">
                    <div class="card">
                        <h2>System Overview</h2>
                        <div style="display: flex; gap: 20px; margin-top: 20px;">
                            <div style="flex: 1; background: #e3f2fd; padding: 15px; border-radius: 5px;">
                                <h3>Total Phones</h3>
                                <p style="font-size: 24px; font-weight: bold;" id="totalPhones"><?php echo $phonesCount ?? 0; ?></p>
                            </div>
                            <div style="flex: 1; background: #e8f5e9; padding: 15px; border-radius: 5px;">
                                <h3>Total Staff</h3>
                                <p style="font-size: 24px; font-weight: bold;" id="totalStaff"><?php echo $staffCount ?? 0; ?></p>
                            </div>
                            <div style="flex: 1; background: #fff3e0; padding: 15px; border-radius: 5px;">
                                <h3>Low Stock Items</h3>
                                <p style="font-size: 24px; font-weight: bold;" id="lowStock"><?php echo $lowStockCount ?? 0; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <h2>Recent Activity</h2>
                        <ul id="recentActivity" style="list-style-type: none; padding: 0;">
                            <?php if (empty($activities)): ?>
                                <li style="padding: 10px; border-bottom: 1px solid #eee;">No recent activity</li>
                            <?php else: ?>
                                <?php foreach ($activities as $activity): ?>
                                    <li style="padding: 10px; border-bottom: 1px solid #eee;">
                                        <?php echo $activity['action']; ?> - <?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                <div id="phonesSection" class="tab-content <?php echo $section === 'phones' ? 'active' : ''; ?>">
                    <div class="search-container">
                        <input type="text" id="phoneSearchInput" placeholder="Search phones..." class="form-control" onkeyup="searchPhones()">
                    </div>
                    
                    <div class="card">
                        <div class="table-responsive">
                            <table id="phoneTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>image</th>
                                        <th>Model</th>
                                        <th>Brand</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Specifications</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                        <tbody>
                            <?php if (!empty($phones)): ?>
                                <?php foreach ($phones as $phone): ?>
                                    <tr>
                                        <td><?php echo $phone['id']; ?></td>
                                        <td>
                                            <?php if (!empty($phone['image_path'])): ?>
                                                <img src="<?php echo $phone['image_path']; ?>" 
                                                    alt="<?php echo htmlspecialchars($phone['brand'].' '.$phone['model']); ?>" 
                                                    style="max-width: 50px; max-height: 50px;">
                                            <?php else: ?>
                                                No Image
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($phone['model']); ?></td>
                                        <td><?php echo htmlspecialchars($phone['brand']); ?></td>
                                        <td>$<?php echo number_format($phone['price'], 2); ?></td>
                                        <td><?php echo $phone['stock']; ?></td>
                                        <td><?php echo htmlspecialchars($phone['specifications']); ?></td>
                                        <td class="action-buttons">
                                            <button class="btn btn-primary" onclick="viewPhoneDetails(<?php echo $phone['id']; ?>)">View</button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="phone_id" value="<?php echo $phone['id']; ?>">
                                                <button type="submit" name="delete_phone" class="btn btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this phone?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center;">No phones found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <button class="btn btn-success" onclick="document.getElementById('addPhoneModal').style.display='block'">Add New Phone</button>
                </div>
                
                <div id="staffSection" class="tab-content <?php echo $section === 'staff' ? 'active' : ''; ?>">
                    <div class="search-container">
                        <input type="text" id="staffSearchInput" placeholder="Search staff..." class="form-control" onkeyup="searchStaff()">
                    </div>
                    
                    <div class="card">
                        <div class="table-responsive">
                            <table id="staffTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>NIC</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Position</th>
                                        <th>Salary</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($staff)): ?>
                                        <?php foreach ($staff as $staff_member): ?>
                                            <tr data-id="<?php echo $staff_member['id']; ?>">
                                                <td><?php echo $staff_member['id']; ?></td>
                                                <td><?php echo htmlspecialchars($staff_member['full_name']); ?></td>
                                                <td><?php echo htmlspecialchars($staff_member['nic']); ?></td>
                                                <td><?php echo htmlspecialchars($staff_member['phone']); ?></td>
                                                <td><?php echo htmlspecialchars($staff_member['email']); ?></td>
                                                <td><?php echo htmlspecialchars($staff_member['position']); ?></td>
                                                <td>$<?php echo number_format($staff_member['salary'], 2); ?></td>
                                                <td class="action-buttons">
                                                    <button class="btn btn-primary" onclick="viewStaffDetails(<?php echo $staff_member['id']; ?>)">View</button>
                                                    <button class="btn btn-warning" onclick="editStaff(<?php echo $staff_member['id']; ?>)">Edit</button>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="staff_id" value="<?php echo $staff_member['id']; ?>">
                                                        <button type="submit" name="delete_staff" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this staff member?')">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" style="text-align: center;">No staff members found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <button class="btn btn-success" onclick="document.getElementById('addStaffModal').style.display='block'">Register New Staff</button>
                </div>
                
                <div id="settingsSection" class="tab-content <?php echo $section === 'settings' ? 'active' : ''; ?>">
                    <div class="card">
                        <h2>Admin Settings</h2>
                        <form method="POST">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" class="form-control" value="<?php echo $_SESSION['admin_name'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?php echo $_SESSION['admin_email'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="current_password">Current Password (to change password):</label>
                                <input type="password" id="current_password" name="current_password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password:</label>
                                <input type="password" id="new_password" name="new_password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password:</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                            </div>
                            <button type="submit" name="update_admin" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Add Phone Modal -->
        <div id="addPhoneModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="document.getElementById('addPhoneModal').style.display='none'">&times;</span>
                <h2>Add New Phone</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="phone_image">Phone Image:</label>
                        <input type="file" id="phone_image" name="phone_image" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="model">Model:</label>
                        <input type="text" id="model" name="model" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="brand">Brand:</label>
                        <input type="text" id="brand" name="brand" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price ($):</label>
                        <input type="number" step="0.01" id="price" name="price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock Quantity:</label>
                        <input type="number" id="stock" name="stock" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="specs">Specifications:</label>
                        <textarea id="specs" name="specs" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="add_phone" class="btn btn-success">Add Phone</button>
                </form>
            </div>
        </div>
        
        <!-- View Phone Details Modal -->
        <div id="viewPhoneModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="document.getElementById('viewPhoneModal').style.display='none'">&times;</span>
                <h2>Phone Details</h2>
                <div id="phoneDetails">
                    <!-- Details will be inserted here by JavaScript -->
                </div>
            </div>
        </div>
        
        <!-- ADD/ EDIT staff Details Modal -->
        <div id="addStaffModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="document.getElementById('addStaffModal').style.display='none'">&times;</span>
                <h2 id="staffModalTitle">Register New Staff</h2>
                <form method="POST">
                    <input type="hidden" name="staff_id" id="editStaffId" value="">
                    <div class="form-group">
                        <label for="staffNIC">NIC Number:</label>
                        <input type="text" id="staffNIC" name="nic" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="staffFullName">Full Name:</label>
                        <input type="text" id="staffFullName" name="full_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="staffAddress">Address:</label>
                        <textarea id="staffAddress" name="address" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="staffPhone">Phone Number:</label>
                        <input type="tel" id="staffPhone" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="staffEmail">Email:</label>
                        <input type="email" id="staffEmail" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="staffPosition">Position:</label>
                        <select id="staffPosition" name="position" class="form-control" required>
                            <option value="">Select Position</option>
                            <option value="Manager">Manager</option>
                            <option value="Sales Staff">Sales Staff</option>
                            <option value="Inventory Staff">Inventory Staff</option>
                            <option value="IT Support">IT Support</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="staffSalary">Salary ($):</label>
                        <input type="number" step="0.01" id="staffSalary" name="salary" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="staffPassword">Password:</label>
                        <input type="password" id="staffPassword" name="password" class="form-control">
                        <small>Leave blank to keep current password when updating</small>
                    </div>
                    <button type="submit" name="add_staff" id="staffSubmitButton" class="btn btn-success">Register Staff</button>
                </form>
            </div>
        </div>
        
        <!-- View Staff Details Modal -->
        <div id="viewStaffModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="document.getElementById('viewStaffModal').style.display='none'">&times;</span>
                <h2>Staff Details</h2>
                <div id="staffDetails">
                    <!-- Details will be inserted here by JavaScript -->
                </div>
            </div>
        </div>
        
        <script>
            // View phone details
            function viewPhoneDetails(phoneId) {
                fetch(`get_phone_details.php?phone_id=${phoneId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const phone = data.phone;
                            document.getElementById('phoneDetails').innerHTML = `
                                <p><strong>ID:</strong> ${phone.id}</p>
                                <p><strong>Model:</strong> ${phone.model}</p>
                                <p><strong>Brand:</strong> ${phone.brand}</p>
                                <p><strong>Price:</strong> $${phone.price.toFixed(2)}</p>
                                <p><strong>In Stock:</strong> ${phone.stock}</p>
                                <p><strong>Release Date:</strong> ${phone.release_date}</p>
                                <h3>Specifications:</h3>
                                <p>${phone.specifications}</p>
                            `;
                            document.getElementById('viewPhoneModal').style.display = 'block';
                        } else {
                            alert(data.message || 'Failed to load phone details');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to load phone details');
                    });
            }
            
            // View staff details
            function viewStaffDetails(staffId) {
                fetch(`get_staff_details.php?staff_id=${staffId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const staff = data.staff;
                            document.getElementById('staffDetails').innerHTML = `
                                <p><strong>ID:</strong> ${staff.id}</p>
                                <p><strong>Name:</strong> ${staff.full_name}</p>
                                <p><strong>NIC:</strong> ${staff.nic}</p>
                                <p><strong>Address:</strong> ${staff.address}</p>
                                <p><strong>Phone:</strong> ${staff.phone}</p>
                                <p><strong>Email:</strong> ${staff.email}</p>
                                <p><strong>Position:</strong> ${staff.position}</p>
                                <p><strong>Salary:</strong> $${staff.salary.toFixed(2)}</p>
                                <p><strong>Join Date:</strong> ${staff.join_date}</p>
                            `;
                            document.getElementById('viewStaffModal').style.display = 'block';
                        } else {
                            alert(data.message || 'Failed to load staff details');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to load staff details');
                    });
            }
            
            // Edit staff (placeholder)
            function editStaff(staffId) {
                alert(`Edit functionality for staff #${staffId} would open an edit form in a real implementation.`);
            }
            
            // Search phones
            function searchPhones() {
                const input = document.getElementById('phoneSearchInput').value.toLowerCase();
                const rows = document.querySelectorAll('#phoneTable tbody tr');
                
                rows.forEach(row => {
                    const cells = row.getElementsByTagName('td');
                    let found = false;
                    
                    for (let j = 0; j < cells.length - 1; j++) { // Skip actions cell
                        if (cells[j].textContent.toLowerCase().includes(input)) {
                            found = true;
                            break;
                        }
                    }
                    
                    row.style.display = found ? '' : 'none';
                });
            }
            
            // Search staff
            function searchStaff() {
                const input = document.getElementById('staffSearchInput').value.toLowerCase();
                const rows = document.querySelectorAll('#staffTable tbody tr');
                
                rows.forEach(row => {
                    const cells = row.getElementsByTagName('td');
                    let found = false;
                    
                    for (let j = 0; j < cells.length - 1; j++) { // Skip actions cell
                        if (cells[j].textContent.toLowerCase().includes(input)) {
                            found = true;
                            break;
                        }
                    }
                    
                    row.style.display = found ? '' : 'none';
                });
            }
            
            // Close modals when clicking outside
            // Close modals when clicking outside
            window.onclick = function(event) {
                if (event.target.className === 'modal') {
                    const modals = document.getElementsByClassName('modal');
                    for (let modal of modals) {
                        modal.style.display = 'none';
                        
                        // Reset the staff form when closing
                        if (modal.id === 'addStaffModal') {
                            const form = modal.querySelector('form');
                            form.reset();
                            form.removeAttribute('data-edit-mode');
                            form.removeAttribute('data-staff-id');
                            form.querySelector('button[type="submit"]').textContent = 'Register Staff';
                            form.querySelector('button[type="submit"]').name = 'add_staff';
                            document.getElementById('staffModalTitle').textContent = 'Register New Staff';
                        }
                    }
                }
            }
        </script>

        <script>
    // View phone details - Updated version
    function viewPhoneDetails(phoneId) {
        // Find the row with the matching phone ID
        const rows = document.querySelectorAll('#phoneTable tbody tr');
        let phoneData = null;
        
        // Search through all rows to find the matching phone
        for (let row of rows) {
            const cells = row.cells;
            if (cells[0].textContent == phoneId) {
                phoneData = {
                    id: cells[0].textContent,
                    image: cells[1].querySelector('img') ? cells[1].querySelector('img').src : null,
                    model: cells[2].textContent,
                    brand: cells[3].textContent,
                    price: cells[4].textContent,
                    stock: cells[5].textContent,
                    specs: cells[6].textContent
                };
                break;
            }
        }
        
        if (!phoneData) {
            alert('Phone details not found');
            return;
        }

        // Create the details HTML
        const detailsHTML = `
            <div class="phone-details-container">
                <p><strong>ID:</strong> ${phoneData.id}</p>
                ${phoneData.image ? `<img src="${phoneData.image}" style="max-width: 200px; max-height: 200px; margin: 10px 0;">` : ''}
                <p><strong>Model:</strong> ${phoneData.model}</p>
                <p><strong>Brand:</strong> ${phoneData.brand}</p>
                <p><strong>Price:</strong> ${phoneData.price}</p>
                <p><strong>In Stock:</strong> ${phoneData.stock}</p>
                <h3>Full Specifications:</h3>
                <p>${phoneData.specs}</p>
            </div>
        `;

        // Display the details in the modal
        document.getElementById('phoneDetails').innerHTML = detailsHTML;
        document.getElementById('viewPhoneModal').style.display = 'block';
    }
    </script>
<script>
            // View staff details
            function viewStaffDetails(staffId) {
                // Find the row with the matching staff ID
                const row = document.querySelector(`#staffTable tr[data-id="${staffId}"]`);
                
                if (!row) {
                    alert('Staff details not found');
                    return;
                }

                // Get all cells from the row
                const cells = row.cells;
                
                // Create the details HTML
                const detailsHTML = `
                    <div class="staff-details">
                        <p><strong>ID:</strong> ${cells[0].textContent}</p>
                        <p><strong>Name:</strong> ${cells[1].textContent}</p>
                        <p><strong>NIC:</strong> ${cells[2].textContent}</p>
                        <p><strong>Phone:</strong> ${cells[3].textContent}</p>
                        <p><strong>Email:</strong> ${cells[4].textContent}</p>
                        <p><strong>Position:</strong> ${cells[5].textContent}</p>
                        <p><strong>Salary:</strong> ${cells[6].textContent}</p>
                    </div>
                `;

                // Display the details in the modal
                document.getElementById('staffDetails').innerHTML = detailsHTML;
                document.getElementById('viewStaffModal').style.display = 'block';
            }

            // Edit staff - this will populate the add staff form with existing data
            function editStaff(staffId) {
                fetch(`?get_staff_details&staff_id=${staffId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const staff = data.staff;
                            
                            // Populate the form with existing data
                            document.getElementById('editStaffId').value = staff.id;
                            document.getElementById('staffNIC').value = staff.nic;
                            document.getElementById('staffFullName').value = staff.full_name;
                            document.getElementById('staffAddress').value = staff.address;
                            document.getElementById('staffPhone').value = staff.phone;
                            document.getElementById('staffEmail').value = staff.email;
                            document.getElementById('staffPosition').value = staff.position;
                            document.getElementById('staffSalary').value = staff.salary;
                            
                            // Change the form to update mode
                            document.getElementById('staffModalTitle').textContent = 'Edit Staff Member';
                            document.getElementById('staffSubmitButton').textContent = 'Update Staff';
                            document.getElementById('staffSubmitButton').name = 'update_staff';
                            
                            // Show the modal
                            document.getElementById('addStaffModal').style.display = 'block';
                        } else {
                            alert(data.message || 'Failed to load staff details');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to load staff details');
                    });
            }

            // Close modals when clicking outside
            window.onclick = function(event) {
                if (event.target.className === 'modal') {
                    const modals = document.getElementsByClassName('modal');
                    for (let modal of modals) {
                        modal.style.display = 'none';
                        
                        // Reset the staff form when closing
                        if (modal.id === 'addStaffModal') {
                            document.getElementById('staffModalTitle').textContent = 'Register New Staff';
                            document.getElementById('staffSubmitButton').textContent = 'Register Staff';
                            document.getElementById('staffSubmitButton').name = 'add_staff';
                            modal.querySelector('form').reset();
                        }
                    }
                }
            }

            // Search staff
            function searchStaff() {
                const input = document.getElementById('staffSearchInput').value.toLowerCase();
                const rows = document.querySelectorAll('#staffTable tbody tr');
                
                rows.forEach(row => {
                    const cells = row.getElementsByTagName('td');
                    let found = false;
                    
                    for (let j = 0; j < cells.length - 1; j++) { // Skip actions cell
                        if (cells[j].textContent.toLowerCase().includes(input)) {
                            found = true;
                            break;
                        }
                    }
                    
                    row.style.display = found ? '' : 'none';
                });
            }
        </script>
    <?php endif; ?>
</body>
</html>
