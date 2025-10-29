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

$sql_comic = "SELECT title FROM tb_komik WHERE komik_id = ?";
$stmt_comic = mysqli_prepare($conn, $sql_comic);
mysqli_stmt_bind_param($stmt_comic, "i", $comic_id);
mysqli_stmt_execute($stmt_comic);
$result_comic = mysqli_stmt_get_result($stmt_comic);
$comic = mysqli_fetch_assoc($result_comic);

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
    <title>Manage Chapters</title>
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
            <h1>Managing Chapters for: <strong><?php echo htmlspecialchars($comic['title']); ?></strong></h1>
            <p>View, edit, or delete chapters for this series. Click "Manage Pages" to edit a chapter's content.</p>
            <a href="dashboard.php" style="display: inline-block; margin-bottom: 20px;">&laquo; Back to All Comics</a>
            <hr>

            <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'updated') {
                    echo '<div class="feedback success">Chapter details updated successfully!</div>';
                } elseif ($_GET['status'] == 'deleted') {
                    echo '<div class="feedback success">Chapter deleted successfully.</div>';
                }
            }
            ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Chapter #</th>
                        <th>Chapter Title</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql_chapters = "SELECT chapter_id, chapter_number, title FROM tb_chapter WHERE komik_id = ? ORDER BY chapter_number ASC";
                        $stmt_chapters = mysqli_prepare($conn, $sql_chapters);
                        mysqli_stmt_bind_param($stmt_chapters, "i", $comic_id);
                        mysqli_stmt_execute($stmt_chapters);
                        $result_chapters = mysqli_stmt_get_result($stmt_chapters);

                        if (mysqli_num_rows($result_chapters) > 0) {
                            while ($chapter = mysqli_fetch_assoc($result_chapters)) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($chapter['chapter_number']) . '</td>';
                                echo '<td>' . htmlspecialchars($chapter['title']) . '</td>';
                                echo '<td>
                                        <a href="manage_pages.php?id=' . $chapter['chapter_id'] . '" class="action-btn edit-btn" style="background-color: #27ae60;">Manage Pages</a>
                                        <a href="edit_chapter.php?id=' . $chapter['chapter_id'] . '" class="action-btn edit-btn">Edit Details</a>
                                        <a href="delete_chapter.php?id=' . $chapter['chapter_id'] . '" class="action-btn delete-btn" onclick="return confirm(\'Are you sure you want to delete this chapter and all its pages?\');">Delete</a>
                                      </td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="3" style="text-align: center;">No chapters have been added for this comic yet.</td></tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>