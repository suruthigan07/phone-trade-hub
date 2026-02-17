<?php
require_once 'db_connect.php';

if (isset($_GET['phone_id'])) {
    $phoneId = $_GET['phone_id'];
    $stmt = $pdo->prepare("SELECT * FROM phones WHERE id = ?");
    $stmt->execute([$phoneId]);
    $phone = $stmt->fetch();
    
    if ($phone) {
        echo json_encode(['success' => true, 'phone' => $phone]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Phone not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No phone ID provided']);
}
?>