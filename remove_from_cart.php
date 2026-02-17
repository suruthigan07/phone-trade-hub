<?php
// remove_from_cart.php
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$itemKey = $_POST['item_key'] ?? null;

if ($itemKey === null || !isset($_SESSION['cart'][$itemKey])) {
    echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
    exit;
}

unset($_SESSION['cart'][$itemKey]);

echo json_encode(['success' => true, 'cart_count' => count($_SESSION['cart'])]);