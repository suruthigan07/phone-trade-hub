<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Phone Trade Hub</title>
    <style>
        /* Reuse your existing styles from home.html */
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

        .register-container {
            max-width: 500px;
            margin: 120px auto 50px;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .register-container h2 {
            color: #007bff;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .form-group input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        .register-btn {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .register-btn:hover {
            background-color: #0056b3;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <img id="Logo" src="image/Logo.png.png" alt="Logo">
        <nav>
            <ul>
                <li><a href="home.html">Home</a></li>
                <li><a href="product.html">Products</a></li>
                <li><a href="cart.html">Cart</a></li>
                <li><a href="#about">About</a></li>
            </ul>
        </nav>
        <div class="login">
            <a href="login.html">Login</a>
        </div>
    </header>

    <div class="register-container">
        <h2>Create Your Account</h2>
        <form id="registerForm">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
            </div>
            <button type="submit" class="register-btn">Register</button>
        </form>
        <div class="login-link">
            Already have an account? <a href="login.html">Login here</a>
        </div>
    </div>

    <footer id="about">
        <!-- Reuse your existing footer from home.html -->
    </footer>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            // Validate passwords match
            if (password !== confirmPassword) {
                alert("Passwords don't match!");
                return;
            }
            
            // Here you would typically send the data to a server
            // For now, we'll just show a success message
            alert('Registration successful! Redirecting to login...');
            
            // Redirect to login page after 1 second
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 1000);
        });
    </script>
</body>
</html>