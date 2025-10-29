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
$comic_id = (int)$_GET['id'];

$sql = "SELECT * FROM tb_komik WHERE komik_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $comic_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$comic = mysqli_fetch_assoc($result);
$current_genres = [];
$sql_current_genres = "SELECT genre_id FROM tb_komik_genre WHERE komik_id = ?";
$stmt_current_genres = mysqli_prepare($conn, $sql_current_genres);
mysqli_stmt_bind_param($stmt_current_genres, "i", $comic_id);
mysqli_stmt_execute($stmt_current_genres);
$result_current_genres = mysqli_stmt_get_result($stmt_current_genres);
while ($row = mysqli_fetch_assoc($result_current_genres)) {
    $current_genres[] = $row['genre_id'];
}

if (!$comic) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Comic</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="dashboard.php" class="nav-button">Dashboard</a>
            <a href="add_comic.php" class="nav-button">Add New Comic</a>
            <a href="add_chapter.php" class="nav-button">Add New Chapter</a>
            <a href="manage_genres.php" class="nav-button">Manage Genres</a> 
            <a href="manage_users.php" class="nav-button">Manage Users</a>
            <a href="../logout.php" class="nav-button logout-button" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
        </div>

        <div class="main-content">
            <h1>Edit Comic Details</h1>
            <p>You are editing: <strong><?php echo htmlspecialchars($comic['title']); ?></strong></p>
            <hr>

            <a href="dashboard.php" class="back-link">&laquo; Back to Comic List</a>

            <div class="form-container">
                <form action="edit_comic_process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="komik_id" value="<?php echo $comic['komik_id']; ?>">

                    <div class="form-group">
                        <label>Title:</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($comic['title']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Author:</label>
                        <input type="text" name="author" value="<?php echo htmlspecialchars($comic['author']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="description"><?php echo htmlspecialchars($comic['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                    <label>Genres:</label>
                    <div class="checkbox-group">
                        <?php
                            $sql_genres = "SELECT * FROM tb_genre ORDER BY name";
                            $result_genres = mysqli_query($conn, $sql_genres);
                            while ($genre = mysqli_fetch_assoc($result_genres)) {
                                $checked = in_array($genre['genre_id'], $current_genres) ? 'checked' : '';
                                echo '<div><input type="checkbox" name="genres[]" value="' . $genre['genre_id'] . '" ' . $checked . '> ' . htmlspecialchars($genre['name']) . '</div>';
                            }
                        ?>
                    </div>
                </div>

                    <div class="form-group">
                        <label>Status:</label>
                        <select name="status">
                            <option value="Ongoing" <?php if ($comic['status'] == 'Ongoing') echo 'selected'; ?>>Ongoing</option>
                            <option value="Completed" <?php if ($comic['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Current Cover Image:</label>
                        <img src="<?php echo htmlspecialchars($comic['cover_image']); ?>" alt="Current Cover" style="width: 100px; border-radius: 4px; margin-bottom: 10px;">
                        <br>
                        <label>Upload New Cover (Optional):</label>
                        <input type="file" name="cover_image">
                    </div>

                    <button type="submit">Update Comic</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>