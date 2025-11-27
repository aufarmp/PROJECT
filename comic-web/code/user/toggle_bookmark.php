<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['komik_id'])) {
    header("Location: homepage.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$komik_id = (int)$_POST['komik_id'];

$sql_check = "SELECT * FROM tb_bookmarks WHERE user_id = ? AND komik_id = ?";
$stmt_check = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $komik_id);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_store_result($stmt_check);

if (mysqli_stmt_num_rows($stmt_check) > 0) {
    $sql_delete = "DELETE FROM tb_bookmarks WHERE user_id = ? AND komik_id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "ii", $user_id, $komik_id);
    mysqli_stmt_execute($stmt_delete);
} else {
    $sql_insert = "INSERT INTO tb_bookmarks (user_id, komik_id) VALUES (?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "ii", $user_id, $komik_id);
    mysqli_stmt_execute($stmt_insert);
}

header("Location: comic_details.php?id=" . $komik_id);
exit();
?>