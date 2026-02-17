<?php
session_start();

// DB connection info
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wad";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only process if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $user_password = $_POST['password'] ?? '';
    
    // First check customers table
    $customerFound = false;
    $stmt = $conn->prepare("SELECT email, user_password, role, fname, lname FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify password (assuming passwords are hashed in DB)
        if ($user_password === $row['user_password']) {
            // Store in session as logged in customer
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['fname'] . ' ' . $row['lname'];
            $customerFound = true;

            // Redirect based on role
            if ($row['role'] === 'user') {
                header("Location: home.php");
                exit();
            } elseif ($row['role'] === 'admin') {
                header("Location: admin.php");
                exit();
            }
        }
    }
    
    // If not found in customers table or password didn't match, check staff table
    if (!$customerFound) {
        $stmt = $conn->prepare("SELECT id, email, password, position, full_name FROM staff WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            if (password_verify($user_password, $row['password'])) {
                // Store in session as logged in staff
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = 'staff';
                $_SESSION['position'] = $row['position'];
                $_SESSION['staff_id'] = $row['id'];
                $_SESSION['name'] = $row['full_name'];
                
                // Redirect staff to their dashboard
                header("Location: staff.php");
                exit();
            }
        }
    }
    
    // If we get here, login failed
    $error = "Invalid email or password.";
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Phone Trade Hub</title>
    <style>
        
        
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('image/login.jpg.jpg') no-repeat;
            background-size: cover;
            background-position: center;
            padding-left: 60px;

        }

        .wrapper {
            width: 420px;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, .2);
            backdrop-filter: blur(20px);
            box-shadow: 0 0 10px rgba(0, 0, 0, .2);
            color: #fff;
            border-radius: 10px;
            padding: 30px 40px;
        }

        .wrapper h1 {
            font-size: 36px;
            text-align: center;
        }

        .wrapper .input-box {
            position: relative;
            width: 100%;
            height: 50px;
            margin: 30px 0;
        }

        .input-box input {
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            outline: none;
            border: 2px solid rgba(255, 255, 255, .2);
            border-radius: 40px;
            font-size: 16px;
            color: #fff;
            padding: 20px 45px 20px 20px;
        }

        .input-box input::placeholder {
            color: #fff;
        }

        .input-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

        .wrapper .remember-forgot {
            display: flex;
            justify-content: space-between;
            font-size: 14.5px;
            margin: -15px 0 15px;
        }

        .remember-forgot label input {
            accent-color: #fff;
            margin-right: 3px;
        }

        .remember-forgot a {
            color: #fff;
            text-decoration: none;
        }

        .remember-forgot a:hover {
            text-decoration: underline;
        }

        .wrapper .btn {
            width: 100%;
            height: 45px;
            background: #fff;
            border: none;
            outline: none;
            border-radius: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
            cursor: pointer;
            font-size: 16px;
            color: #333;
            font-weight: 600;
        }

        .wrapper .register-link {
            font-size: 14.5px;
            text-align: center;
            margin: 20px 0 15px;
        }

        .register-link p a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link p a:hover {
            text-decoration: underline;
        }

        
        .bx_bxs-user,
        .bx_bxs-lock-alt {
            width: 20px; 
            height: 20px;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php if (isset($error)): ?>
            <div style="color: red; text-align: center; margin-bottom: 20px;"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <h1>Login</h1>
            <div class="input-box">
                <input type="text" name="email" placeholder="Email" required>
                <img class="bx_bxs-user" src="image/user.png">
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <img class="bx_bxs-lock-alt" src="image/lock.png">
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox">Remember me</label>
                <a href="#">Forgot password?</a>
            </div>
            <button type="submit" class="btn">Login</button>
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </form>
    </div>
</body>
</html>