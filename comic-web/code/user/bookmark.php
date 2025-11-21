<?php
session_start();
include '../connection.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

if (isset($_GET['remove_id'])) {
    $komik_id_to_remove = (int)$_GET['remove_id'];

    $sql_delete = "DELETE FROM tb_bookmarks WHERE user_id = ? AND komik_id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "ii", $user_id, $komik_id_to_remove);
    mysqli_stmt_execute($stmt_delete);

    header("Location: bookmark.php?status=removed");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookmarks - Comic Web</title>
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
            <h2 class="page-heading">My Bookmarks</h2>

            <?php
            if (isset($_GET['status']) && $_GET['status'] == 'removed') {
                echo '<div class="feedback success" style="max-width: 1200px; margin: 0 auto 20px auto;">Bookmark removed successfully!</div>';
            }
            ?>

            <div class="comic-grid">
                <?php
                $sql = "SELECT k.* FROM tb_komik k
                        JOIN tb_bookmarks b ON k.komik_id = b.komik_id
                        WHERE b.user_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    while ($comic = mysqli_fetch_assoc($result)) {
                        echo '<div class="comic-card">';
                        echo '  <a href="comic_details.php?id=' . $comic['komik_id'] . '">';
                        echo '      <img src="' . htmlspecialchars($comic['cover_image']) . '" alt="' . htmlspecialchars($comic['title']) . '">';
                        echo '  </a>';
                        echo '  <div class="card-content">';
                        echo '      <h3>' . htmlspecialchars($comic['title']) . '</h3>';
                        echo '      <p>By: ' . htmlspecialchars($comic['author']) . '</p>';
                        echo '      <a href="comic_details.php?id=' . $comic['komik_id'] . '" class="read-more-btn">Read More</a>';
                        echo '      <a href="bookmark.php?remove_id=' . $comic['komik_id'] . '" class="remove-btn" onclick="return confirm(\'Remove this bookmark?\');">Remove</a>';
                        echo '  </div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="no-results">You have not bookmarked any comics yet.</p>';
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

        <script>
        function showLogoutModal(event) {
            event.preventDefault();
            document.getElementById('logoutModal').style.display = 'flex';
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        // Close modal when clicking outside the modal content
        document.getElementById('logoutModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeLogoutModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeLogoutModal();
            }
        });
        </script>

    </div>
</body>
</html>