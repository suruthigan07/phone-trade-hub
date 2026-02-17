<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Trade Hub</title>
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

        .banner {
            width: 100%;
            height: 400px;
            background:  url('image/banner.jpg') no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 35px;
            font-weight: bold;
            text-shadow: 3px 3px 12px rgba(0, 0, 0, 0.8);
            margin-top: 60px;
            padding-top: 100px;
        }

        h1 {
            color: #ffffff;
            font-size: 28px;
        }

        p {
            color: #ffffff;
            font-size: 18px;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 25px;
            transition: background 0.3s, transform 0.3s;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.2);
        }

        .btn:hover {
            background-color: orange;
            transform: scale(1.1);
        }

        .container {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .container h2{
            color: rgb(3, 13, 146);
            font-size: 36px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 30px;
        }

        .features-section {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin: 40px 0;
        }

        .feature-card {
            background:rgb(118, 184, 250) ;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.59);
            width: 300px;
            margin: 20px;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.32);
        }

        .feature-icon {
            font-size: 50px;
            color: #007bff;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            color:white ;
            font-size: 22px;
            margin-bottom: 15px;
        }

        .feature-card p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
        }

        .how-it-works {
            background-color: #f0f8ff;
            padding: 60px 20px;
            margin: 40px 0;
        }

        .steps {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .step {
            width: 250px;
            margin: 20px;
            position: relative;
        }

        .step-number {
            background-color: #007bff;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 20px;
            font-weight: bold;
        }

        .step h3 {
            color:rgb(15, 163, 12);
            margin-bottom: 15px;
        }

        .step p {
            color: #666;
            font-size: 16px;
        }

        .vision-section {
            padding: 60px 20px;
            background-color: #fff;
        }

        .vision-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .vision-content h2 {
            color: rgb(3, 13, 146);
            margin-bottom: 30px;
            font-size: 30px;
        }

        .vision-content p {
            color: #555;
            font-size: 18px;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .vision-points {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: 40px;
        }

        .vision-point {
            width: 250px;
            margin: 20px;
            padding: 25px;
            background-color:rgb(118, 184, 250);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.43);
        }

        .vision-point h3 {
            color:white;
            margin-bottom: 15px;
        }

        .vision-point p {
            color: #666;
            font-size: 16px;
        }

        .cta-section {
            background:rgb(118, 184, 250);
            color:rgb(15, 163, 12);
            padding: 80px 20px;
            text-align: center;
            margin: 40px 0;
        }

        .cta-section h2 {
            color: white;
            font-size: 36px;
            margin-bottom: 20px;
        }

        .cta-section p {
            font-size: 18px;
            max-width: 700px;
            margin: 0 auto 30px;
        }

        .cta-btn {
            display: inline-block;
            padding: 15px 30px;
            background-color: white;
            color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .cta-btn:hover {
            background-color:rgb(248, 248, 250);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        *{
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
            color: white;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <img id="Logo" src="image/Logo.png.png" alt="Logo">
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="product.php">Products</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="#about">About</a></li>
            </ul>
        </nav>
        <div class="login">
            <a href="login.php">Login</a>
        </div>
    </header>
    <div class="banner">
        <div>
            <h1>Welcome to Phone Trade Hub</h1>
            <p>Your Global Marketplace for Buying and Selling Smartphones</p>
            <a href="product.php" class="btn">Explore Now</a>
        </div>
    </div>
    
    <div class="container">
        <h2>Why Choose Phone Trade Hub?</h2>
        <p style="color: #555; font-size: 18px; max-width: 800px; margin: 0 auto 40px;">
            We connect buyers and sellers worldwide, offering the best deals on smartphones with complete transparency and security.
        </p>
        
        <div class="features-section">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-globe"></i>
                </div>
                <h3>Global Marketplace</h3>
                <p style="color: #666;">Buy from or sell to customers worldwide. We handle all the logistics so you don't have to worry about international transactions.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure Transactions</h3>
                <p style="color: #666;">Our escrow payment system ensures your money is safe until you receive your purchase. Fraud protection on all transactions.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-search-dollar"></i>
                </div>
                <h3>Best Prices</h3>
                <p style="color: #666;">Get the best deals on new and used smartphones. Our price comparison tools help you make informed decisions.</p>
            </div>
        </div>
    </div>
    
    <div class="how-it-works">
        <div class="container">
            <h2>How It Works</h2>
            <p style="color: #555; font-size: 18px; max-width: 800px; margin: 0 auto 40px;">
                Buying and selling smartphones has never been easier. Here's how our platform works:
            </p>
            
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Create an Account</h3>
                    <p>Sign up as a buyer or seller in just a few minutes. Verification takes less than 5 minutes.</p>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>List or Browse</h3>
                    <p>Sellers can list their devices with detailed specifications. Buyers can browse our extensive catalog.</p>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Make a Deal</h3>
                    <p>Negotiate prices, ask questions, and make offers directly through our secure messaging system.</p>
                </div>
                
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Complete Transaction</h3>
                    <p>Payment is held securely until the buyer confirms receipt. We provide shipping labels and tracking.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="vision-section">
        <div class="vision-content">
            <h2>Our Vision</h2>
            <p>At Phone Trade Hub, we envision a world where buying and selling smartphones is seamless, secure, and accessible to everyone, regardless of location. We're committed to revolutionizing the mobile device marketplace through innovation and customer-focused solutions.</p>
            
            <div class="vision-points">
                <div class="vision-point">
                    <h3>Global Accessibility</h3>
                    <p>Breaking down geographical barriers to create a truly global marketplace for mobile devices.</p>
                </div>
                
                <div class="vision-point">
                    <h3>Technological Innovation</h3>
                    <p>Leveraging cutting-edge technology to ensure secure, fast, and user-friendly transactions.</p>
                </div>
                
                <div class="vision-point">
                    <h3>Sustainable Commerce</h3>
                    <p>Promoting the circular economy by extending the lifecycle of mobile devices through responsible trade.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="cta-section">
        <h2>Ready to Join Our Community?</h2>
        <p>Whether you're looking to upgrade your phone or sell your current device, Phone Trade Hub makes it simple, safe, and rewarding.</p>
        <a href="product.html" class="cta-btn">Start Trading Now</a>
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
        document.addEventListener("DOMContentLoaded", function() {
            const btn = document.querySelector(".btn");
            btn.addEventListener("mouseover", function() {
                btn.style.transform = "scale(1.1)";
            });
            btn.addEventListener("mouseleave", function() {
                btn.style.transform = "scale(1)";
            });
        });
        function buy(){
            window.location.href="product.html";
        }
    </script>
</body>
</html>