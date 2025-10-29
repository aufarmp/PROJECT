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
$komik_id_to_delete = (int)$_GET['id'];

// --- PREPARE FOR DELETION ---
$sql_cover = "SELECT cover_image FROM tb_komik WHERE komik_id = ?";
$stmt_cover = mysqli_prepare($conn, $sql_cover);
mysqli_stmt_bind_param($stmt_cover, "i", $komik_id_to_delete);
mysqli_stmt_execute($stmt_cover);
$result_cover = mysqli_stmt_get_result($stmt_cover);
$comic_cover = mysqli_fetch_assoc($result_cover);
$cover_image_path = $comic_cover ? $comic_cover['cover_image'] : null;

$sql_chapters = "SELECT chapter_id, chapter_number FROM tb_chapter WHERE komik_id = ?";
$stmt_chapters = mysqli_prepare($conn, $sql_chapters);
mysqli_stmt_bind_param($stmt_chapters, "i", $komik_id_to_delete);
mysqli_stmt_execute($stmt_chapters);
$result_chapters = mysqli_stmt_get_result($stmt_chapters);

// --- DELETION PROCESS ---
$sql_delete_genres = "DELETE FROM tb_komik_genre WHERE komik_id = ?";
$stmt_delete_genres = mysqli_prepare($conn, $sql_delete_genres);
mysqli_stmt_bind_param($stmt_delete_genres, "i", $komik_id_to_delete);
mysqli_stmt_execute($stmt_delete_genres);

$sql_delete_bookmarks = "DELETE FROM tb_bookmarks WHERE komik_id = ?";
$stmt_delete_bookmarks = mysqli_prepare($conn, $sql_delete_bookmarks);
mysqli_stmt_bind_param($stmt_delete_bookmarks, "i", $komik_id_to_delete);
mysqli_stmt_execute($stmt_delete_bookmarks);

while ($chapter = mysqli_fetch_assoc($result_chapters)) {
    $chapter_id = $chapter['chapter_id'];
    $chapter_number = $chapter['chapter_number'];

    $sql_pages = "SELECT image_url FROM tb_pages WHERE chapter_id = ?";
    $stmt_pages = mysqli_prepare($conn, $sql_pages);
    mysqli_stmt_bind_param($stmt_pages, "i", $chapter_id);
    mysqli_stmt_execute($stmt_pages);
    $result_pages = mysqli_stmt_get_result($stmt_pages);

    while ($page = mysqli_fetch_assoc($result_pages)) {
        $server_file_path = $_SERVER['DOCUMENT_ROOT'] . $page['image_url'];
        if (file_exists($server_file_path)) {
            @unlink($server_file_path);
        }
    }

    $sql_delete_pages = "DELETE FROM tb_pages WHERE chapter_id = ?";
    $stmt_delete_pages = mysqli_prepare($conn, $sql_delete_pages);
    mysqli_stmt_bind_param($stmt_delete_pages, "i", $chapter_id);
    mysqli_stmt_execute($stmt_delete_pages);

    $chapter_folder_path = $_SERVER['DOCUMENT_ROOT'] . '/comic_project/comic_web/assets/comic_pages/' . $komik_id_to_delete . '/' . $chapter_number;
    if (is_dir($chapter_folder_path)) {
        @rmdir($chapter_folder_path); 
    }
}

$sql_delete_chapters = "DELETE FROM tb_chapter WHERE komik_id = ?";
$stmt_delete_chapters = mysqli_prepare($conn, $sql_delete_chapters);
mysqli_stmt_bind_param($stmt_delete_chapters, "i", $komik_id_to_delete);
mysqli_stmt_execute($stmt_delete_chapters);

$sql_delete_comic = "DELETE FROM tb_komik WHERE komik_id = ?";
$stmt_delete_comic = mysqli_prepare($conn, $sql_delete_comic);
mysqli_stmt_bind_param($stmt_delete_comic, "i", $komik_id_to_delete);
mysqli_stmt_execute($stmt_delete_comic);

if ($cover_image_path) {
    $cover_server_path = $_SERVER['DOCUMENT_ROOT'] . $cover_image_path;
    if (file_exists($cover_server_path)) {
        @unlink($cover_server_path);
    }
}

header("Location: dashboard.php?status=deleted");
exit();

?>