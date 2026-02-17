<?php
require_once 'db_connect.php';

// Get all products with brand information
$stmt = $pdo->prepare("
    SELECT p.*, b.brand_name, b.brand_logo
    FROM phones p
    JOIN brands b ON p.brand = b.brand_name
    ORDER BY p.model
");
$stmt->execute();
$products = $stmt->fetchAll();

// Get all brands for filter
$stmt = $pdo->query("SELECT brand_name FROM brands ORDER BY brand_name");
$brands = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Phone Trade Hub</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            text-align: center;
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
        }

        .filters {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .filters select, .filters input {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .phonelist {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 20px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card:hover {
            transform: translateY(-10px);
        }

        .card img {
            width: 100%;
            border-radius: 10px;
        }

        .card h3 {
            font-size: 24px;
            margin: 15px 0;
            color: #333;
        }

        .card p {
            color: #333;
            font-size: 16px;
            margin: 10px 0;
        }

        .card .price {
            font-size: 22px;
            color: #007BFF;
            font-weight: bold;
        }

        .card .offer {
            font-size: 18px;
            color: #28a745;
            font-weight: bold;
        }

        .card .features {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .card .features li {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
        }

        .card .buy-btn, .card .wishlist-btn, .card .add-to-cart-btn {
            display: inline-block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background 0.3s, transform 0.3s;
            margin-top: 15px;
            margin-right: 10px;
        }

        .card .buy-btn {
            background-color: #28a745;
        }

        .card .buy-btn:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .card .wishlist-btn {
            background-color: #ffc107;
        }

        .card .wishlist-btn:hover {
            background-color: #e0a800;
            transform: scale(1.05);
        }

        .card .add-to-cart-btn {
            background-color: #007bff;
        }

        .card .add-to-cart-btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .quantity-control {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 15px;
        }

        .quantity-control button {
            padding: 5px 10px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .quantity-control button:hover {
            background-color: #0056b3;
        }

        .quantity-control input {
            width: 40px;
            text-align: center;
            margin: 0 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        
        .hidden {
            display: none;
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
        <h2>All Products</h2>
        <div class="filters">
            <select id="brandFilter">
                <option value="">Filter by Brand</option>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?= htmlspecialchars($brand['brand_name']) ?>">
                        <?= htmlspecialchars($brand['brand_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" id="searchFilter" placeholder="Search by name..." oninput="filterProducts()">
        </div>
        <div class="phonelist" id="productContainer">
            <?php foreach ($products as $product): 
                // Calculate discount if available (assuming you might add this later)
                $discount = isset($product['discount']) ? $product['discount'] : 0;
                $discountedPrice = $discount > 0 ? $product['price'] * (1 - ($discount / 100)) : $product['price'];
                
                // Split specifications into features
                $features = !empty($product['specifications']) ? explode('|', $product['specifications']) : [];
            ?>
                <div class="card" data-brand="<?= htmlspecialchars($product['brand_name']) ?>">
                    <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['model']) ?>">
                    <h3><?= htmlspecialchars($product['model']) ?></h3>
                    <p class="price">$<?= number_format($discountedPrice, 2) ?></p>
                    <?php if ($discount > 0): ?>
                        <p class="offer"><?= $discount ?>% off</p>
                        <p class="original-price" style="text-decoration: line-through; color: #777;">$<?= number_format($product['price'], 2) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($features)): ?>
                        <ul class="features">
                            <?php foreach ($features as $feature): ?>
                                <li><?= htmlspecialchars(trim($feature)) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div class="quantity-control">
                        <button onclick="updateQuantity('<?= htmlspecialchars($product['model']) ?>', -1)">-</button>
                        <input type="text" id="<?= htmlspecialchars($product['model']) ?>-quantity" value="1" readonly>
                        <button onclick="updateQuantity('<?= htmlspecialchars($product['model']) ?>', 1)">+</button>
                    </div>
                    <a href="#" class="buy-btn" onclick="buy('<?= htmlspecialchars($product['model']) ?>', <?= $discountedPrice ?>)">Buy Now</a>
                    <a href="#" class="wishlist-btn" onclick="addToWishlist('<?= htmlspecialchars($product['model']) ?>', <?= $discountedPrice ?>)">Add to Wishlist</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function filterProducts() {
            const brandFilter = document.getElementById('brandFilter').value.toLowerCase();
            const searchTerm = document.getElementById('searchFilter').value.toLowerCase();
            const cards = document.querySelectorAll('.card');
            
            cards.forEach(card => {
                const brand = card.getAttribute('data-brand').toLowerCase();
                const productName = card.querySelector('h3').textContent.toLowerCase();
                
                const brandMatch = brandFilter === '' || brand === brandFilter;
                const searchMatch = productName.includes(searchTerm);
                
                if (brandMatch && searchMatch) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }

        // filters
        document.getElementById('brandFilter').addEventListener('change', filterProducts);
        document.getElementById('searchFilter').addEventListener('input', filterProducts);

        // update product quantity
        function updateQuantity(productName, change) {
            const quantityInput = document.getElementById(`${productName}-quantity`);
            let quantity = parseInt(quantityInput.value) + change;
            if (quantity < 1) quantity = 1;
            if (quantity > 10) quantity = 10; // Limit to 10 per order
            quantityInput.value = quantity;
        }

        // add wishlist
        function addToWishlist(productName, productPrice) {
            const quantity = parseInt(document.getElementById(`${productName}-quantity`).value);
            addToCart(productName, productPrice, quantity);
            alert(`${productName} added to wishlist and cart!`);
        }

        // add to cart
        function addToCart(productName, productPrice, quantity = 1) {
            let cartItems = JSON.parse(localStorage.getItem("cart")) || [];
            const existingProduct = cartItems.find(item => item.name === productName);

            if (existingProduct) {
                existingProduct.quantity += quantity;
            } else {
                cartItems.push({ name: productName, price: productPrice, quantity });
            }

            localStorage.setItem("cart", JSON.stringify(cartItems));
        }

        // Buy Now
        function buy(productName, productPrice) {
            const quantity = parseInt(document.getElementById(`${productName}-quantity`).value);
            addToCart(productName, productPrice, quantity);
            window.location.href = "cart.php";
        }
    </script>
</body>
</html>