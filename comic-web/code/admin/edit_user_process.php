<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = (int)$_POST['user_id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $password = $_POST['password']; 

    if (!empty($password)) {
        
        $sql = "UPDATE tb_user SET username=?, email=?, password=?, role=? WHERE user_id=?";
        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "ssssi", $username, $email, $password, $role, $user_id);

    } else {
        $sql = "UPDATE tb_user SET username=?, email=?, role=? WHERE user_id=?";
        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $role, $user_id);
    }

    if (mysqli_stmt_execute($stmt)) {

        header("Location: manage_users.php?status=user_updated");
        exit();
    } else {
        echo "Error: Could not update the user.";
    }
}
?>