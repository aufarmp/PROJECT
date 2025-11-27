<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}
$user_id_to_delete = (int)$_GET['id'];

if ($user_id_to_delete == $_SESSION['user_id']) {
    header("Location: manage_users.php?status=delete_self_error");
    exit();
}

$sql = "DELETE FROM tb_user WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id_to_delete);

if (mysqli_stmt_execute($stmt)) {
    header("Location: manage_users.php?status=user_deleted");
    exit();
} else {
    echo "Error: Could not delete the user.";
}
?>