<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $comic_id = (int)$_POST['comic_id'];
    $chapter_number = $_POST['chapter_number'];
    $chapter_title = mysqli_real_escape_string($conn, $_POST['chapter_title']);

    $sql_chapter = "INSERT INTO tb_chapter (komik_id, chapter_number, title) VALUES (?, ?, ?)";
    $stmt_chapter = mysqli_prepare($conn, $sql_chapter);
    mysqli_stmt_bind_param($stmt_chapter, "ids", $comic_id, $chapter_number, $chapter_title);
    
    if (mysqli_stmt_execute($stmt_chapter)) {
        $new_chapter_id = mysqli_insert_id($conn);

        if (isset($_FILES['pages'])) {
            $page_count = count($_FILES['pages']['name']);

            for ($i = 0; $i < $page_count; $i++) {
                if ($_FILES['pages']['error'][$i] == 0) {
                    $page_tmp_name = $_FILES['pages']['tmp_name'][$i];
                    $page_name = basename($_FILES['pages']['name'][$i]);
                    $page_number = $i + 1;

                    $upload_dir = '../../assets/comic_pages/' . $comic_id . '/' . $chapter_number . '/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $server_path = $upload_dir . $page_name;
                    
                    if (move_uploaded_file($page_tmp_name, $server_path)) {
                        $db_image_path = '/comic_project/comic-web/assets/comic_pages/' . $comic_id . '/' . $chapter_number . '/' . $page_name;

                        $sql_page = "INSERT INTO tb_pages (chapter_id, page_number, image_url) VALUES (?, ?, ?)";
                        $stmt_page = mysqli_prepare($conn, $sql_page);
                        mysqli_stmt_bind_param($stmt_page, "iis", $new_chapter_id, $page_number, $db_image_path);
                        mysqli_stmt_execute($stmt_page);
                    }
                }
            }
            header("Location: add_chapter.php?status=success");
            exit();
        }
    } else {
        header("Location: add_chapter.php?status=error");
        exit();
    }
}
?>