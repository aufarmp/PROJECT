<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_genre'])) {
    $genre_name = mysqli_real_escape_string($conn, $_POST['genre_name']);
    if (!empty($genre_name)) {
        $sql = "INSERT INTO tb_genre (name) VALUES (?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $genre_name);
        mysqli_stmt_execute($stmt);
        header("Location: manage_genres.php?status=added");
        exit();
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $sql_junction = "DELETE FROM tb_komik_genre WHERE genre_id = ?";
    $stmt_junction = mysqli_prepare($conn, $sql_junction);
    mysqli_stmt_bind_param($stmt_junction, "i", $delete_id);
    mysqli_stmt_execute($stmt_junction);

    $sql_genre = "DELETE FROM tb_genre WHERE genre_id = ?";
    $stmt_genre = mysqli_prepare($conn, $sql_genre);
    mysqli_stmt_bind_param($stmt_genre, "i", $delete_id);
    mysqli_stmt_execute($stmt_genre);
    header("Location: manage_genres.php?status=deleted");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Genres</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="dashboard.php" class="nav-button">Dashboard</a>
            <a href="add_comic.php" class="nav-button">Add New Comic</a>
            <a href="add_chapter.php" class="nav-button">Add New Chapter</a>
            <a href="manage_genres.php" class="nav-button active">Manage Genres</a>
            <a href="manage_users.php" class="nav-button">Manage Users</a>
            <a href="../logout.php" class="nav-button logout-button" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
        </div>

        <div class="main-content">
            <h1>Manage Genres</h1>
            <p>Add, view, or delete comic genres from the system.</p>
            <hr>

            <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'added') echo '<div class="feedback success">Genre added successfully!</div>';
                if ($_GET['status'] == 'deleted') echo '<div class="feedback success">Genre deleted successfully.</div>';
            }
            ?>

            <div class="form-container" style="margin-bottom: 30px;">
                <h2>Add New Genre</h2>
                <form action="manage_genres.php" method="POST">
                    <div class="form-group">
                        <label for="genre_name">Genre Name:</label>
                        <input type="text" id="genre_name" name="genre_name" required>
                    </div>
                    <button type="submit" name="add_genre">Add Genre</button>
                </form>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Genre ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT genre_id, name FROM tb_genre ORDER BY genre_id ASC";
                        $result = mysqli_query($conn, $sql);
                        while ($genre = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo '<td>' . $genre['genre_id'] . '</td>';
                            echo '<td>' . htmlspecialchars($genre['name']) . '</td>';
                            echo '<td>
                                    <a href="manage_genres.php?delete_id=' . $genre['genre_id'] . '" class="action-btn delete-btn" onclick="return confirm(\'Are you sure you want to delete this genre?\');">Delete</a>
                                  </td>';
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>