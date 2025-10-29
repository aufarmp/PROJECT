<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id']; 

if (!isset($_GET['id'])) {
    header("Location: homepage.php");
    exit();
}
$chapter_id = (int)$_GET['id'];

$sql_history = "INSERT INTO tb_histori_bacaan (user_id, chapter_id)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE last_read_at = CURRENT_TIMESTAMP";
$stmt_history = mysqli_prepare($conn, $sql_history);
mysqli_stmt_bind_param($stmt_history, "ii", $user_id, $chapter_id);
mysqli_stmt_execute($stmt_history);

$sql_pages = "SELECT * FROM tb_pages WHERE chapter_id = ? ORDER BY page_number ASC";
$stmt_pages = mysqli_prepare($conn, $sql_pages);
mysqli_stmt_bind_param($stmt_pages, "i", $chapter_id);
mysqli_stmt_execute($stmt_pages);
$result_pages = mysqli_stmt_get_result($stmt_pages);
$sql_info = "SELECT ch.chapter_number, k.komik_id, k.title AS comic_title
             FROM tb_chapter ch
             JOIN tb_komik k ON ch.komik_id = k.komik_id
             WHERE ch.chapter_id = ?";
$stmt_info = mysqli_prepare($conn, $sql_info);
mysqli_stmt_bind_param($stmt_info, "i", $chapter_id);
mysqli_stmt_execute($stmt_info);
$result_info = mysqli_stmt_get_result($stmt_info);
$info = mysqli_fetch_assoc($result_info);

if (!$info) {
    header("Location: homepage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($info['comic_title']) . ' - Chapter ' . htmlspecialchars($info['chapter_number']); ?></title>
    <link rel="stylesheet" href="../../assets/css/user_style.css">
</head>
<body>

    <header class="navbar">
        <div class="container">
            <h1 class="site-title">Comic Web</h1>
            </div>
    </header>

    <main class="container">
        <div class="reader-navigation">
            <a href="comic_details.php?id=<?php echo $info['komik_id']; ?>" class="back-link">
                &laquo; Back to Chapter List
            </a>
            <h2><?php echo htmlspecialchars($info['comic_title']) . ' - Chapter ' . htmlspecialchars($info['chapter_number']); ?></h2>
        </div>

        <div class="reader-container">
            <?php
            if (mysqli_num_rows($result_pages) > 0) {
                while ($page = mysqli_fetch_assoc($result_pages)) {
                    echo '<img src="' . htmlspecialchars($page['image_url']) . '" alt="Page ' . htmlspecialchars($page['page_number']) . '">';
                }
            } else {
                echo '<p class="no-pages">The pages for this chapter have not been uploaded yet.</p>';
            }
            ?>
        </div>

        <div class="reader-navigation" style="margin-top: 20px;">
            <a href="comic_details.php?id=<?php echo $info['komik_id']; ?>" class="back-link">
                &laquo; Back to Chapter List
            </a>
        </div>
        
    </main>
    
    <footer class="site-footer">
        <p>&copy; <?php echo date("Y"); ?> Comic Web Project</p>
    </footer>

</body>
</html>