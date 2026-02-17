<?php
require_once 'db_connect.php';

if (isset($_GET['staff_id'])) {
    $staffId = $_GET['staff_id'];
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE id = ?");
    $stmt->execute([$staffId]);
    $staff = $stmt->fetch();
    
    if ($staff) {
        echo json_encode(['success' => true, 'staff' => $staff]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Staff not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No staff ID provided']);
}
?>