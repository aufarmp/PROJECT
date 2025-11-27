<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}
$chapter_id = (int)$_GET['id'];

$sql_info = "SELECT komik_id, chapter_number FROM tb_chapter WHERE chapter_id = ?";
$stmt_info = mysqli_prepare($conn, $sql_info);
mysqli_stmt_bind_param($stmt_info, "i", $chapter_id);
mysqli_stmt_execute($stmt_info);
$result_info = mysqli_stmt_get_result($stmt_info);
$chapter_info = mysqli_fetch_assoc($result_info);

if (!$chapter_info) {
    header("Location: dashboard.php");
    exit();
}
$komik_id = $chapter_info['komik_id'];
$chapter_number = $chapter_info['chapter_number'];

$sql_pages = "SELECT image_url FROM tb_pages WHERE chapter_id = ?";
$stmt_pages = mysqli_prepare($conn, $sql_pages);
mysqli_stmt_bind_param($stmt_pages, "i", $chapter_id);
mysqli_stmt_execute($stmt_pages);
$result_pages = mysqli_stmt_get_result($stmt_pages);

while ($page = mysqli_fetch_assoc($result_pages)) {
    $server_file_path = $_SERVER['DOCUMENT_ROOT'] . $page['image_url'];
    if (file_exists($server_file_path)) {
        unlink($server_file_path); // Fungsi yang menghapus file
    }
}

$sql_delete_pages = "DELETE FROM tb_pages WHERE chapter_id = ?";
$stmt_delete_pages = mysqli_prepare($conn, $sql_delete_pages);
mysqli_stmt_bind_param($stmt_delete_pages, "i", $chapter_id);
mysqli_stmt_execute($stmt_delete_pages);

$chapter_folder_path = $_SERVER['DOCUMENT_ROOT'] . '/comic_project/comic-web/assets/comic_pages/' . $komik_id . '/' . $chapter_number;
if (is_dir($chapter_folder_path)) {
    @rmdir($chapter_folder_path);
}

$sql_delete_chapter = "DELETE FROM tb_chapter WHERE chapter_id = ?";
$stmt_delete_chapter = mysqli_prepare($conn, $sql_delete_chapter);
mysqli_stmt_bind_param($stmt_delete_chapter, "i", $chapter_id);
mysqli_stmt_execute($stmt_delete_chapter);

header("Location: manage_chapters.php?id=" . $komik_id . "&status=deleted");
exit();
?>