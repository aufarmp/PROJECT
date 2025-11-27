<?php
session_start();
include '../connection.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: homepage.php");
    exit();
}
$comic_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id']; 

$sql_comic = "SELECT k.*, GROUP_CONCAT(g.name SEPARATOR ', ') AS genres
              FROM tb_komik k
              LEFT JOIN tb_komik_genre kg ON k.komik_id = kg.komik_id
              LEFT JOIN tb_genre g ON kg.genre_id = g.genre_id
              WHERE k.komik_id = ?
              GROUP BY k.komik_id";
$stmt_comic = mysqli_prepare($conn, $sql_comic);
mysqli_stmt_bind_param($stmt_comic, "i", $comic_id);
mysqli_stmt_execute($stmt_comic);
$result_comic = mysqli_stmt_get_result($stmt_comic);
$comic = mysqli_fetch_assoc($result_comic);

if (!$comic) {
    header("Location: homepage.php");
    exit();
}

$sql_chapters = "SELECT * FROM tb_chapter WHERE komik_id = ? ORDER BY chapter_number ASC";
$stmt_chapters = mysqli_prepare($conn, $sql_chapters);
mysqli_stmt_bind_param($stmt_chapters, "i", $comic_id);
mysqli_stmt_execute($stmt_chapters);
$result_chapters = mysqli_stmt_get_result($stmt_chapters);

$sql_check = "SELECT * FROM tb_bookmarks WHERE user_id = ? AND komik_id = ?";
$stmt_check = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $comic_id);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_store_result($stmt_check);
$is_bookmarked = (mysqli_stmt_num_rows($stmt_check) > 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($comic['title']); ?></title>
    <link rel="stylesheet" href="../../assets/css/user_style.css">
</head>
<body>

    <div class="user-container">
        
        <div class="sidebar">
            <h2>Comic Web</h2>
            <a href="homepage.php" class="nav-button">Browse</a>
            <a href="bookmark.php" class="nav-button">My Bookmarks</a>
            <a href="history.php" class="nav-button">History</a>
            <a href="profile.php" class="nav-button">Profile</a> 
            <a href="../logout.php" class="nav-button logout-button" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
        </div>

        <div class="main-content">
            <a href="homepage.php" class="back-link">&laquo; Back to Homepage</a>
            
            <div class="comic-details-container">
                <div class="info-wrapper">
                    <img class="comic-cover" src="<?php echo htmlspecialchars($comic['cover_image']); ?>" alt="Cover of <?php echo htmlspecialchars($comic['title']); ?>">
                    <div class="comic-info">
                        <h1><?php echo htmlspecialchars($comic['title']); ?></h1>

                        <h3>By: <?php echo htmlspecialchars($comic['author']); ?></h3>
                        <p class="status"><strong>Status:</strong> <?php echo htmlspecialchars($comic['status']); ?></p>

                        <?php if (!empty($comic['genres'])): ?>
                            <div class="genres-container">
                                <strong>Genres:</strong>
                                <?php
                                $genres = explode(', ', $comic['genres']);
                                foreach ($genres as $genre) {
                                    echo '<span class="genre-tag">' . htmlspecialchars($genre) . '</span>';
                                }
                                ?>
                            </div>
                        <?php endif; ?>

                        <p class="description"><?php echo nl2br(htmlspecialchars($comic['description'])); ?></p>

                        <form action="toggle_bookmark.php" method="POST" style="margin-bottom: 20px;">
                            <input type="hidden" name="komik_id" value="<?php echo $comic_id; ?>">
                            <button type="submit" class="bookmark-btn">
                                <?php echo $is_bookmarked ? 'Remove from Bookmarks' : 'Add to Bookmarks'; ?>
                            </button>
                        </form>

                    </div>
                </div>

                <div class="chapters-section">
                    <h2>Chapters</h2>
                    <ul class="chapter-list">
                        <?php
                        if (mysqli_num_rows($result_chapters) > 0) {
                            while ($chapter = mysqli_fetch_assoc($result_chapters)) {
                                echo '<li>';
                                echo '<a href="reader.php?id=' . $chapter['chapter_id'] . '">';
                                echo 'Chapter ' . htmlspecialchars($chapter['chapter_number']) . ': ' . htmlspecialchars($chapter['title']);
                                echo '</a>';
                                echo '</li>';
                            }
                        } else {
                            echo '<li class="no-chapters">No chapters have been added yet.</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div> 
            <footer class="content-footer">
                <p>&copy; <?php echo date("Y"); ?> Comic Web Project</p>
            </footer>
        </div> 
    </div> 
</body>
</html>