<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Comic</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="dashboard.php" class="nav-button">Dashboard</a>
            <a href="add_comic.php" class="nav-button active">Add New Comic</a>
            <a href="add_chapter.php" class="nav-button">Add New Chapter</a>
            <a href="manage_genres.php" class="nav-button">Manage Genres</a> 
            <a href="manage_users.php" class="nav-button">Manage Users</a>
            <a href="../logout.php" class="nav-button logout-button" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
        </div>

        <div class="main-content">
            <h1>Add New Comic</h1>
            <p>Fill out the form below to add a new comic series to the library.</p>
            <hr>

            <div class="form-container">
                <form action="add_comic_process.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Title:</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Author:</label>
                        <input type="text" name="author" required>
                    </div>
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Genres:</label>
                        <div class="checkbox-group">
                            <?php
                                $sql_genres = "SELECT * FROM tb_genre ORDER BY name";
                                $result_genres = mysqli_query($conn, $sql_genres);
                                while ($genre = mysqli_fetch_assoc($result_genres)) {
                                    echo '<div><input type="checkbox" name="genres[]" value="' . $genre['genre_id'] . '"> ' . htmlspecialchars($genre['name']) . '</div>';
                                }
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Status:</label>
                        <select name="status">
                            <option value="Ongoing">Ongoing</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cover Image:</label>
                        <input type="file" name="cover_image" required>
                    </div>
                    <button type="submit">Add Comic</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>