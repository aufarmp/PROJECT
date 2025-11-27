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
                <a href="history.php" class="nav-button">History</a>
                <a href="profile.php" class="nav-button">Profile</a> 
                <a href="#" class="nav-button logout-button" onclick="showLogoutModal(event)">Logout</a>
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
                        echo '      <button class="remove-btn" onclick="showRemoveHistoryModal(' . $item['history_id'] . ', \'' . htmlspecialchars(addslashes($item['comic_title'])) . '\', ' . $item['chapter_number'] . ')">Remove</button>';
                        echo '  </div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="no-results">You haven\'t read any comics yet.</p>';
                }
                ?>
            </div>

            <footer class="content-footer">
                <p>&copy; <?php echo date("Y"); ?> Comic Web Project</p>
            </footer>
        </div>

        <!-- Logout Confirmation Modal -->
        <div id="logoutModal" class="modal-overlay">
            <div class="modal-content">
                <h3>Confirm Logout</h3>
                <p>Are you sure you want to log out?</p>
                <div class="modal-buttons">
                    <a href="../logout.php" class="modal-btn confirm">Yes, Logout</a>
                    <button class="modal-btn cancel" onclick="closeLogoutModal()">Cancel</button>
                </div>
            </div>
        </div>

        <!-- Remove History Confirmation Modal -->
        <div id="removeHistoryModal" class="modal-overlay">
            <div class="modal-content">
                <h3>Remove History</h3>
                <p id="removeHistoryText">Are you sure you want to remove this reading history?</p>
                <div class="modal-buttons">
                    <a href="#" id="removeHistoryConfirm" class="modal-btn confirm">Yes, Remove</a>
                    <button class="modal-btn cancel" onclick="closeRemoveHistoryModal()">Cancel</button>
                </div>
            </div>
        </div>

        <script>
        function showLogoutModal(event) {
            event.preventDefault();
            document.getElementById('logoutModal').style.display = 'flex';
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        function showRemoveHistoryModal(historyId, comicTitle, chapterNumber) {
            document.getElementById('removeHistoryText').textContent = 
                'Are you sure you want to remove "' + comicTitle + ' (Chapter ' + chapterNumber + ')" from your reading history?';
            
            const confirmLink = document.getElementById('removeHistoryConfirm');
            confirmLink.href = 'history.php?remove_history_id=' + historyId;
            
            document.getElementById('removeHistoryModal').style.display = 'flex';
        }

        function closeRemoveHistoryModal() {
            document.getElementById('removeHistoryModal').style.display = 'none';
        }

        // Close modals when clicking outside the modal content
        document.getElementById('logoutModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeLogoutModal();
            }
        });

        document.getElementById('removeHistoryModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeRemoveHistoryModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeLogoutModal();
                closeRemoveHistoryModal();
            }
        });
        </script>

    </div>
</body>
</html>