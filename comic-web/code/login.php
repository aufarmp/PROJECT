<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include 'connection.php'; 

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/homepage.php");
    }
    exit(); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM tb_user WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            // JS utk nimpa halaman login dengan halaman dashboard/homepage
            echo '<script type="text/javascript">';
            if ($user['role'] == 'admin') {
                echo 'window.location.replace("admin/dashboard.php");';
            } else {
                echo 'window.location.replace("user/homepage.php");';
            }
            echo '</script>';
            
            // exit() buat stop eksekusi PHP setelah eksekusi JS
            exit();

        } else {
            // Password Salah
            $_SESSION['login_error'] = "Invalid password.";
            header("Location: login.php");
            exit();
        }

    } else {
        // Username Salah
        $_SESSION['login_error'] = "Invalid username/email.";
        header("Location: login.php");
        exit();
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/login_style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2>Login to Your Account</h2>
            <p>Please enter your credentials to proceed.</p>

            <?php
            if (isset($_SESSION['login_error'])) {
                echo '<div class="feedback error">' . $_SESSION['login_error'] . '</div>';
                unset($_SESSION['login_error']);
        
            } elseif (isset($_GET['status']) && $_GET['status'] == 'registered') {
                echo '<div class="feedback success">Registration successful! Please log in with your account.</div>';
            }
            ?>

            <form action="login.php" method="POST">
                <label for="username">Username or email</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Login</button>
            </form>

                <div class="register-link">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>