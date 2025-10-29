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
    <title>Add New Chapter</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="dashboard.php" class="nav-button">Dashboard</a>
            <a href="add_comic.php" class="nav-button">Add New Comic</a>
            <a href="add_chapter.php" class="nav-button active">Add New Chapter</a>
            <a href="manage_genres.php" class="nav-button">Manage Genres</a> 
            <a href="manage_users.php" class="nav-button">Manage Users</a>
            <a href="../logout.php" class="nav-button logout-button" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
        </div>

        <div class="main-content">
            <h1>Add New Chapter</h1>
            <p>Select a comic and upload the pages for a new chapter.</p>
            <hr>

            <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'success') {
                    echo '<div class="feedback success">New chapter and pages added successfully!</div>';
                } elseif ($_GET['status'] == 'error') {
                    echo '<div class="feedback error">An error occurred while adding the chapter.</div>';
                }
            }
            ?>

            <div class="form-container">
                <form action="add_chapter_process.php" method="POST" enctype="multipart/form-data">

                    <div class="form-group">
                        <label for="comic_id">Select Comic:</label>
                        <select name="comic_id" id="comic_id" required>
                            <option value="">-- Choose a Comic --</option>
                            <?php
                                $sql = "SELECT komik_id, title FROM tb_komik ORDER BY title";
                                $result = mysqli_query($conn, $sql);
                                while ($comic = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . $comic['komik_id'] . '">' . htmlspecialchars($comic['title']) . '</option>';
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="chapter_number">Chapter Number:</label>
                        <input type="number" step="0.1" name="chapter_number" id="chapter_number" required>
                    </div>

                    <div class="form-group">
                        <label for="chapter_title">Chapter Title (Optional):</label>
                        <input type="text" name="chapter_title" id="chapter_title">
                    </div>

                    <div class="form-group">
                        <label for="pages">Upload Pages (Select multiple files):</label>
                        <input type="file" name="pages[]" id="pages" required multiple>
                    </div>

                    <button type="submit">Add Chapter</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>