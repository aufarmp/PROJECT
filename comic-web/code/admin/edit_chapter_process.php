<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $chapter_id = (int)$_POST['chapter_id'];
    $komik_id = (int)$_POST['komik_id'];
    $chapter_number = $_POST['chapter_number'];
    $chapter_title = mysqli_real_escape_string($conn, $_POST['chapter_title']);

    $sql = "UPDATE tb_chapter SET chapter_number = ?, title = ? WHERE chapter_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "dsi", $chapter_number, $chapter_title, $chapter_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: manage_chapters.php?id=" . $komik_id . "&status=updated");
        exit();
    } else {
        echo "Error: Could not update the chapter.";
    }

} else {
    header("Location: dashboard.php");
    exit();
}
?>