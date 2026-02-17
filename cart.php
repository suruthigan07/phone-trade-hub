<?php
session_start();
require_once 'db_connect.php';

$cartItems = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Phone Trade Hub</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        header {
            background-color: black;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 10px 25px;
            height: 60px;
            justify-content: space-between;
        }

        #Logo {
            width: 150px;
            height: auto;
        }

        nav {
            padding: 15px 0;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }

        nav ul li {
            margin: 0 20px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 20px;
            transition: color 0.3s;
        }

        nav ul li a:hover {
            color: #00aaff;
        }

        .login a {
            color: white;
            text-decoration: none;
            font-size: 20px;
            transition: color 0.3s;
        }

        .login a:hover {
            color: #00aaff;
        }

        .container {
            padding: 20px;
            margin-top: 80px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .cart-items {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-item img {
            width: 100px;
            height: auto;
            border-radius: 5px;
            margin-right: 20px;
        }

        .item-details {
            flex-grow: 1;
        }

        .item-price {
            font-weight: bold;
            color: #007BFF;
            margin: 5px 0;
        }

        .item-quantity {
            display: flex;
            align-items: center;
        }

        .item-quantity input {
            width: 50px;
            text-align: center;
            margin: 0 10px;
        }

        .remove-item {
            color: #dc3545;
            cursor: pointer;
            margin-left: 20px;
        }

        .cart-summary {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: #28a745;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .checkout-btn:hover {
            background-color: #218838;
        }

        .empty-cart {
            text-align: center;
            padding: 50px;
            font-size: 18px;
        }
         footer {
            background-color: #000;
            color: #fff;
            padding: 50px 0;
            margin-top: 50px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            padding: 0 0px;
        }

        .footer-section {
            flex: 1;
            min-width: 200px;
            margin-bottom: 20px;
            text-decoration: none;
            
        }

        .footer-section h3 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #00aaff;
        }

        .footer-section p {
            font-size: 16px;
            line-height: 1.6;
            color: #ccc;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 10px;
        }

        .footer-section ul li a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section ul li a:hover {
            color: #00aaff;
        }

        

        .footer-bottom {
            text-align: center;
            margin-top: 30px;
            padding-top: -10px;
            font-size: 14px;
            color: #ccc;
            
        }

        .footer-bottom a {
            color: #00aaff;
            text-decoration: none;
        }

        .footer-bottom a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <img id="Logo" src="image/Logo.png.png" alt="Logo">
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="product.php">Products</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="home.php#about">About</a></li>
            </ul>
        </nav>
        <div class="login">
            <a href="login.php">Login</a>
        </div>
    </header>

    <div class="container">
        <h2>Your Shopping Cart</h2>
        
        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <p>Your cart is empty</p>
                <a href="products.php" class="checkout-btn" style="background-color: #007bff;">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($cartItems as $key => $item): 
                    // In a real application, you would fetch product details from database
                    $itemTotal = $item['price'] * $item['quantity'];
                    $total += $itemTotal;
                ?>
                    <div class="cart-item" data-product="<?= htmlspecialchars($item['product_name']) ?>">
                        <img src="image/<?= strtolower(str_replace(' ', '', $item['product_name'])) ?>.jpg" alt="<?= htmlspecialchars($item['product_name']) ?>">
                        <div class="item-details">
                            <h3><?= htmlspecialchars($item['product_name']) ?></h3>
                            <p class="item-price">$<?= number_format($item['price'], 2) ?></p>
                            <div class="item-quantity">
                                <button onclick="updateCartQuantity('<?= $key ?>', -1)">-</button>
                                <input type="text" value="<?= $item['quantity'] ?>" readonly>
                                <button onclick="updateCartQuantity('<?= $key ?>', 1)">+</button>
                                <span class="remove-item" onclick="removeFromCart('<?= $key ?>')">Remove</span>
                            </div>
                        </div>
                        <div class="item-total">
                            $<?= number_format($itemTotal, 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div style="display: flex; justify-content: space-between; margin: 15px 0;">
                    <span>Subtotal:</span>
                    <span>$<?= number_format($total, 2) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin: 15px 0;">
                    <span>Shipping:</span>
                    <span>Free</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin: 15px 0; font-size: 20px; font-weight: bold;">
                    <span>Total:</span>
                    <span>$<?= number_format($total, 2) ?></span>
                </div>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </div>

     <footer id="about">
        <div class="footer-container">
            
            <div class="footer-section">
                <h3>About Us</h3>
                <p>Phone Trade Hub is your one-stop destination for buying and selling smartphones globally. We offer the best deals on top brands and ensure a seamless shopping experience.</p>
            </div>

            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="home.html">Home</a></li>
                    <li><a href="product.html">Products</a></li>
                    <li><a href="cart.html">Cart</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="login.html">Login</a></li>
                </ul>
            </div>

            
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: phonetradehub@gmail.com</p>
                <p>Phone: +94 762 853 525</p>
                <p>Address:NotherUni , Kandharmadam ,  Jaffna <br>  SriLanka</p>
            </div>

        </div>  
        
        <div class="footer-bottom">
            <p>&copy; 2025 Phone Trade Hub. All rights reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
        </div>
    </footer>

    <script>
        function updateCartQuantity(itemKey, change) {
            // Send AJAX request to update quantity
            const formData = new FormData();
            formData.append('item_key', itemKey);
            formData.append('change', change);
            
            fetch('update_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload(); // Refresh to show updated cart
                } else {
                    alert('Error updating cart: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function removeFromCart(itemKey) {
            if (confirm('Are you sure you want to remove this item?')) {
                // Send AJAX request to remove item
                const formData = new FormData();
                formData.append('item_key', itemKey);
                
                fetch('remove_from_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload(); // Refresh to show updated cart
                    } else {
                        alert('Error removing item: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
</body>
</html>