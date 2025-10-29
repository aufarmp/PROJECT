<?php
session_start();
include '../connection.php';

// Security Check: Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// 1. Get the ID of the chapter to edit
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}
$chapter_id = (int)$_GET['id'];

// 2. Fetch the existing data for this chapter
$sql = "SELECT * FROM tb_chapter WHERE chapter_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $chapter_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$chapter = mysqli_fetch_assoc($result);

if (!$chapter) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Chapter</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="dashboard.php" class="nav-button active">View All Comics</a>
            <a href="add_comic.php" class="nav-button">Add New Comic</a>
            <a href="add_chapter.php" class="nav-button">Add New Chapter</a>
            <a href="manage_genres.php" class="nav-button">Manage Genres</a>
            <a href="manage_users.php" class="nav-button">Manage Users</a>
            <a href="../logout.php" class="nav-button logout-button" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
        </div>

        <div class="main-content">
            <h1>Edit Chapter Details</h1>
            <p>You are editing Chapter <?php echo htmlspecialchars($chapter['chapter_number']); ?></p>
            <hr>
            
            <a href="manage_chapters.php?id=<?php echo $chapter['komik_id']; ?>" class="back-link">&laquo; Back to Chapter List</a>

            <div class="form-container">
                <form action="edit_chapter_process.php" method="POST">
                    <input type="hidden" name="chapter_id" value="<?php echo $chapter['chapter_id']; ?>">
                    <input type="hidden" name="komik_id" value="<?php echo $chapter['komik_id']; ?>">

                    <div class="form-group">
                        <label>Chapter Number:</label>
                        <input type="number" step="0.1" name="chapter_number" value="<?php echo htmlspecialchars($chapter['chapter_number']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Chapter Title (Optional):</label>
                        <input type="text" name="chapter_title" value="<?php echo htmlspecialchars($chapter['title']); ?>">
                    </div>

                    <button type="submit">Update Chapter</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>