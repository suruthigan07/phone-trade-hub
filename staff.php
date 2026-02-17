<?php
// Database configuration and session start
session_start();

require_once 'db_connect.php';

// Fetch all phones for the phones table
$stmt = $pdo->query("SELECT * FROM phones ORDER BY id DESC");
$phones = $stmt->fetchAll();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
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
    header("Location: ".$_SERVER['PHP_SELF']);
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
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Handle staff password change
if (isset($_POST['update_password'])) {
    $email = $_POST['email'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validate inputs
    if (empty($email) || empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error_message = 'All fields are required';
    } elseif ($newPassword !== $confirmPassword) {
        $error_message = 'New passwords do not match';
    } else {
        // Get staff by email
        $stmt = $pdo->prepare("SELECT * FROM staff WHERE email = ?");
        $stmt->execute([$email]);
        $staff = $stmt->fetch();
        
        if (!$staff) {
            $error_message = 'Staff member not found';
        } elseif (!password_verify($currentPassword, $staff['password'])) {
            $error_message = 'Current password is incorrect';
        } else {
            // Update password
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE staff SET password = ? WHERE email = ?");
            $stmt->execute([$passwordHash, $email]);
            
            $success_message = 'Password updated successfully';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Phone Management Dashboard</title>
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

        /* Settings modal styles */
        .settings-form {
            margin-top: 20px;
        }

        .settings-form .form-group {
            margin-bottom: 15px;
        }
        
        .phone-image {
            max-width: 50px;
            max-height: 50px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Phone Management</h2>
                <p>Staff Dashboard</p>
            </div>
            <ul class="sidebar-menu">
                <li class="active"><i>📱</i> Phone Inventory</li>
                <li onclick="document.getElementById('addPhoneModal').style.display='block'"><i>➕</i> Add New Phone</li>
                <li onclick="document.getElementById('settingsModal').style.display='block'"><i>👤</i> Change Password</li>
                <li onclick="window.location='?logout'"><i>🚪</i> Logout</li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1>Phone Inventory Management</h1>
                <div>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['staff_name'] ?? 'Staff'); ?></span>
                </div>
            </div>
            
            <?php if (isset($success_message)): ?>
                <div class="card" style="background-color: #dff0d8; color: #3c763d;"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="card" style="background-color: #f2dede; color: #a94442;"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search phones..." class="form-control" onkeyup="searchPhones()">
            </div>
            
            <div class="card">
                <div class="table-responsive">
                    <table id="phoneTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
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
                                    <tr data-id="<?php echo $phone['id']; ?>">
                                        <td><?php echo $phone['id']; ?></td>
                                        <td>
                                            <?php if (!empty($phone['image_path'])): ?>
                                                <img src="<?php echo $phone['image_path']; ?>" 
                                                    alt="<?php echo htmlspecialchars($phone['brand'].' '.$phone['model']); ?>" 
                                                    class="phone-image">
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
    
    <!-- Change Password Modal -->
    <div id="settingsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('settingsModal').style.display='none'">&times;</span>
            <h2>Change Password</h2>
            <div class="settings-form">
                <form method="POST">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['staff_email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="current_password">Current Password:</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" name="update_password" class="btn btn-primary">Change Password</button>
                </form>
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
                            ${phone.image_path ? `<img src="${phone.image_path}" alt="${phone.brand} ${phone.model}" style="max-width: 200px; max-height: 200px; display: block; margin-bottom: 15px;">` : ''}
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
        
        // Search phones
        function searchPhones() {
            const input = document.getElementById('searchInput').value.toLowerCase();
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
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                const modals = document.getElementsByClassName('modal');
                for (let modal of modals) {
                    modal.style.display = 'none';
                }
            }
        }
    </script>

    <script>
    // View phone details
    function viewPhoneDetails(phoneId) {
        // Find the row with the matching phone ID
        const row = document.querySelector(`#phoneTable tr[data-id="${phoneId}"]`);
        
        if (!row) {
            alert('Phone details not found');
            return;
        }

        // Get all cells from the row
        const cells = row.cells;
        
        // Extract the data from the cells
        const id = cells[0].textContent;
        const image = cells[1].querySelector('img') ? cells[1].querySelector('img').src : 'No Image';
        const model = cells[2].textContent;
        const brand = cells[3].textContent;
        const price = cells[4].textContent;
        const stock = cells[5].textContent;
        const specs = cells[6].textContent;

        // Create the details HTML
        const detailsHTML = `
            <p><strong>ID:</strong> ${id}</p>
            ${image !== 'No Image' ? `<img src="${image}" alt="${brand} ${model}" style="max-width: 200px; max-height: 200px; display: block; margin-bottom: 15px;">` : ''}
            <p><strong>Model:</strong> ${model}</p>
            <p><strong>Brand:</strong> ${brand}</p>
            <p><strong>Price:</strong> ${price}</p>
            <p><strong>In Stock:</strong> ${stock}</p>
            <h3>Full Specifications:</h3>
            <p>${specs}</p>
        `;

        // Display the details in the modal
        document.getElementById('phoneDetails').innerHTML = detailsHTML;
        document.getElementById('viewPhoneModal').style.display = 'block';
    }
</script>
</body>
</html>