<?php
session_start();
include '../connection.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

if (isset($_GET['remove_history_id'])) {
    $history_id_to_remove = (int)$_GET['remove_history_id'];

    $sql_delete = "DELETE FROM tb_histori_bacaan WHERE history_id = ? AND user_id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "ii", $history_id_to_remove, $user_id);
    mysqli_stmt_execute($stmt_delete);

    header("Location: history.php?status=removed");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reading History - Comic Web</title>
    <link rel="stylesheet" href="../../assets/css/user_style.css">
</head>
<body>

    <div class="user-container">

            <div class="sidebar">
                <h2>Comic Web</h2>
                <a href="homepage.php" class="nav-button">Browse</a>
                <a href="bookmark.php" class="nav-button">My Bookmarks</a>
                <a href="history.php" class="nav-button active">History</a>
                <a href="profile.php" class="nav-button">Profile</a> 
                <a href="../logout.php" class="nav-button logout-button" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
            </div>

        <div class="main-content">
            <h2 class="page-heading">My Reading History</h2>

            <?php
            if (isset($_GET['status']) && $_GET['status'] == 'removed') {
                echo '<div class="feedback success" style="max-width: 1200px; margin: 0 auto 20px auto;">History entry removed successfully!</div>';
            }
            ?>

            <div class="comic-grid">
                <?php
                $sql = "SELECT
                            h.history_id, h.last_read_at, 
                            ch.chapter_id, ch.chapter_number, ch.title AS chapter_title,
                            k.komik_id, k.title AS comic_title, k.cover_image
                        FROM tb_histori_bacaan h
                        JOIN tb_chapter ch ON h.chapter_id = ch.chapter_id
                        JOIN tb_komik k ON ch.komik_id = k.komik_id
                        WHERE h.user_id = ?
                        ORDER BY h.last_read_at DESC";

                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    while ($item = mysqli_fetch_assoc($result)) {
                        echo '<div class="comic-card">';
                        echo '  <a href="reader.php?id=' . $item['chapter_id'] . '">';
                        echo '      <img src="' . htmlspecialchars($item['cover_image']) . '" alt="' . htmlspecialchars($item['comic_title']) . '">';
                        echo '  </a>';
                        echo '  <div class="card-content">';
                        echo '      <h3>' . htmlspecialchars($item['comic_title']) . '</h3>';
                        echo '      <p>Last read: Ch. ' . htmlspecialchars($item['chapter_number']) . '</p>';
                        echo '      <p style="font-size: 12px; color: #888;">' . date('M d, Y H:i', strtotime($item['last_read_at'])) . '</p>';
                        echo '      <a href="reader.php?id=' . $item['chapter_id'] . '" class="read-more-btn">Continue Reading</a>';
                        echo '      <button class="remove-btn" onclick="showRemoveModal(' . $item['history_id'] . ', \'' . htmlspecialchars(addslashes($item['comic_title'])) . '\', \'Ch. ' . htmlspecialchars(addslashes($item['chapter_number'])) . '\')">Remove</button>';
                        echo '  </div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="no-results">You haven\'t read any comics yet.</p>';
                }
                ?>
            </div>

            <!-- Remove Confirmation Modal -->
            <div class="modal-overlay" id="removeModal">
                <div class="modal">
                    <div class="modal-header">
                        <h3>Remove History Entry</h3>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to remove the reading history for "<span id="comicTitle"></span>" (Chapter <span id="chapterInfo"></span>)?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modal-btn cancel" onclick="hideRemoveModal()">Cancel</button>
                        <button type="button" class="modal-btn confirm" id="confirmRemoveBtn">Remove</button>
                    </div>
                </div>
            </div>

            <footer class="content-footer">
                <p>&copy; <?php echo date("Y"); ?> Comic Web Project</p>
            </footer>
        </div>
    </div>

    <script>
        let currentRemoveId = null;

        function showRemoveModal(historyId, comicTitle, chapterInfo) {
            currentRemoveId = historyId;
            document.getElementById('comicTitle').textContent = comicTitle;
            document.getElementById('chapterInfo').textContent = chapterInfo;
            document.getElementById('removeModal').classList.add('active');
        }

        function hideRemoveModal() {
            document.getElementById('removeModal').classList.remove('active');
            currentRemoveId = null;
        }

        function confirmRemove() {
            if (currentRemoveId) {
                window.location.href = 'history.php?remove_history_id=' + currentRemoveId;
            }
        }

        // Event listeners
        document.getElementById('confirmRemoveBtn').addEventListener('click', confirmRemove);

        // Close modal when clicking outside
        document.getElementById('removeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideRemoveModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideRemoveModal();
            }
        });
    </script>

</body>
</html>