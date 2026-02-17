<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$productName = $_POST['product_name'] ?? '';
$productPrice = $_POST['product_price'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

if (empty($productName)) {
    echo json_encode(['success' => false, 'message' => 'Product name is required']);
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if product already in cart
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['product_name'] === $productName) {
        $item['quantity'] += $quantity;
        $found = true;
        break;
    }
}

// If not found, add new item
if (!$found) {
    $_SESSION['cart'][] = [
        'product_name' => $productName,
        'price' => $productPrice,
        'quantity' => $quantity,
        'added_at' => date('Y-m-d H:i:s')
    ];
}

echo json_encode(['success' => true, 'cart_count' => count($_SESSION['cart'])]);