<?php
// update_cart.php
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$itemKey = $_POST['item_key'] ?? null;
$change = (int)($_POST['change'] ?? 0);

if ($itemKey === null || !isset($_SESSION['cart'][$itemKey])) {
    echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
    exit;
}

$newQuantity = $_SESSION['cart'][$itemKey]['quantity'] + $change;

if ($newQuantity < 1) {
    unset($_SESSION['cart'][$itemKey]);
} else {
    $_SESSION['cart'][$itemKey]['quantity'] = $newQuantity;
}

echo json_encode(['success' => true, 'cart_count' => count($_SESSION['cart'])]);