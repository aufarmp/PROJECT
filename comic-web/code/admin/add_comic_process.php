<?php
session_start();
include '../connection.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        
        $image_info = $_FILES['cover_image'];
        $image_name = $image_info['name'];
        $image_tmp_name = $image_info['tmp_name'];

        $upload_dir = '../../assets/comic_cover/'; 

        $unique_filename = time() . '_' . basename($image_name);
        $server_path = $upload_dir . $unique_filename;

        if (move_uploaded_file($image_tmp_name, $server_path)) {
            
            $db_image_path = '/comic_project/comic-web/assets/comic_cover/' . $unique_filename;

            $sql = "INSERT INTO tb_komik (title, author, description, status, cover_image) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssss", $title, $author, $description, $status, $db_image_path);

            if (mysqli_stmt_execute($stmt)) {
                
                $new_comic_id = mysqli_insert_id($conn);

                if (!empty($_POST['genres']) && $new_comic_id > 0) {
                    $sql_genre_link = "INSERT INTO tb_komik_genre (komik_id, genre_id) VALUES (?, ?)";
                    $stmt_genre_link = mysqli_prepare($conn, $sql_genre_link);
                    
                    foreach ($_POST['genres'] as $genre_id) {
                        mysqli_stmt_bind_param($stmt_genre_link, "ii", $new_comic_id, $genre_id);
                        mysqli_stmt_execute($stmt_genre_link);
                    }
                }
                
                header("Location: dashboard.php?status=success");
                exit();

            } else {
                echo "Error: Could not save comic details to database.";
            }

        } else {
            echo "Error: Could not move the uploaded file.";
        }
    } else {
        echo "Error: No file uploaded or an error occurred.";
    }
}
?>