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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="dashboard.php" class="nav-button active">Dashboard</a>
            <a href="add_comic.php" class="nav-button">Add New Comic</a>
            <a href="add_chapter.php" class="nav-button">Add New Chapter</a>
            <a href="manage_genres.php" class="nav-button">Manage Genres</a> 
            <a href="manage_users.php" class="nav-button">Manage Users</a>
            <a href="../logout.php" class="nav-button logout-button" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
        </div>

        <div class="main-content">
            <h1>Manage Comics</h1>
            <p>Here is a list of all comics currently in the database.</p>
            <hr>

            <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'success') {
                    echo '<div class="feedback success">New comic added successfully!</div>';
                } elseif ($_GET['status'] == 'updated') {
                    echo '<div class="feedback success">Comic details updated successfully!</div>';
                } elseif ($_GET['status'] == 'deleted') {
                    echo '<div class="feedback success">Comic deleted successfully.</div>';
                } elseif ($_GET['status'] == 'error') {
                    echo '<div class="feedback error">An error occurred. Please try again.</div>';
                }
            }
            ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT komik_id, cover_image, title, author, status FROM tb_komik ORDER BY komik_id ASC";
                        $result = mysqli_query($conn, $sql);
                        while ($comic = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo '<td>' . $comic['komik_id'] . '</td>';
                            echo '<td><img src="' . htmlspecialchars($comic['cover_image']) . '" alt="Cover" style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;"></td>';
                            echo '<td>' . htmlspecialchars($comic['title']) . '</td>';
                            echo '<td>' . htmlspecialchars($comic['author']) . '</td>';
                            echo '<td>' . htmlspecialchars($comic['status']) . '</td>';
                            echo '<td>
                                    <a href="manage_chapters.php?id=' . $comic['komik_id'] . '" class="action-btn edit-btn" style="background-color: #f39c12;">Chapters</a>
                                    <a href="edit_comic.php?id=' . $comic['komik_id'] . '" class="action-btn edit-btn">Edit</a>
                                    <a href="delete_comic.php?id=' . $comic['komik_id'] . '" class="action-btn delete-btn" onclick="return confirm(\'Are you sure you want to delete this comic and ALL its chapters/pages?\');">Delete</a>
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