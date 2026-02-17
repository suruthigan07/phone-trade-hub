<?php
// Only process the form if it's submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // DB connection
    $conn = new mysqli("localhost", "root", "", "wad");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get inputs
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'user'; // Default role

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit();
    }

    // Save to DB
    $sql = "INSERT INTO customers (email, user_password, role, fname, lname, username)
            VALUES ('$email', '$password', '$role', '$fname', '$lname', '$username')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
            alert('registered Successful!');
            window.location.href = 'home.php';
        </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Phone Trade Hub</title>
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
            margin: 20px 0;
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

        .wrapper .login-link {
            font-size: 14.5px;
            text-align: center;
            margin: 20px 0 15px;
        }

        .login-link p a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link p a:hover {
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
        <form action="register.php" method="POST">
            <h1>Register</h1>
            <div class="input-box">
                <input type="text" name="fname" placeholder="First Name" required>
                <img class="bx_bxs-user" src="image/user.png">
            </div>
            <div class="input-box">
                <input type="text" name="lname" placeholder="Last Name" required>
                <img class="bx_bxs-user" src="image/user.png">
            </div>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
                <img class="bx_bxs-user" src="image/name.jpg.png">
            </div>
            <div class="input-box">
                <input type="email" name="email" placeholder="Email Address" required>
                <img class="bx_bxs-user" src="image/mail.jpg.png">
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <img class="bx_bxs-lock-alt" src="image/lock.png">
            </div>
            <div class="input-box">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <img class="bx_bxs-lock-alt" src="image/lock.png">
            </div>
            <button type="submit" class="btn">Register</button>
            <div class="login-link">
                <p>Already have an account? <a href="login.html">Login</a></p>
            </div>
        </form>

    </div>
</body>
</html>