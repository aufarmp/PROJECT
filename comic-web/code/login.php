<?php
session_start();
include 'connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM tb_user WHERE (username = '$username' OR email = '$username') AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/homepage.php");
        }
        exit();
    } else {
        
        $_SESSION['login_error'] = "Invalid username/email or password.";
        header("Location: login.php");
        exit();
    }
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