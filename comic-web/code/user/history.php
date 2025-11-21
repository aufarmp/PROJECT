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
    <style>
        /* Custom Popup Styles */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .popup-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .popup-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
            text-align: center;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .popup-overlay.active .popup-content {
            transform: translateY(0);
        }

        .popup-title {
            margin-top: 0;
            color: #333;
            font-size: 1.5rem;
        }

        .popup-message {
            margin: 15px 0 25px;
            color: #666;
            line-height: 1.5;
        }

        .popup-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .popup-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
            min-width: 100px;
            text-decoration: none;
            display: inline-block;
            box-sizing: border-box;
        }

        .popup-confirm {
            background-color: #e74c3c;
            color: white;
        }

        .popup-confirm:hover {
            background-color: #c0392b;
        }

        .popup-cancel {
            background-color: #ecf0f1;
            color: #333;
        }

        .popup-cancel:hover {
            background-color: #bdc3c7;
        }

        /* Update logout button style */
        .logout-button {
            position: relative;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Custom Logout Popup -->
    <div class="popup-overlay" id="logoutPopup">
        <div class="popup-content">
            <h3 class="popup-title">Confirm Logout</h3>
            <p class="popup-message">Are you sure you want to log out?</p>
            <div class="popup-buttons">
                <button class="popup-btn popup-cancel" id="cancelLogout">Cancel</button>
                <a href="../logout.php" class="popup-btn popup-confirm" id="confirmLogout">Logout</a>
            </div>
        </div>
    </div>

    <div class="user-container">

        <div class="sidebar">
            <h2>Comic Web</h2>
            <a href="homepage.php" class="nav-button">Browse</a>
            <a href="bookmark.php" class="nav-button">My Bookmarks</a>
            <a href="history.php" class="nav-button">History</a>
            <a href="profile.php" class="nav-button">Profile</a> 
            <a class="nav-button logout-button" id="logoutTrigger">Logout</a>
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
                        echo '      <a href="history.php?remove_history_id=' . $item['history_id'] . '" class="remove-btn" onclick="return confirm(\'Remove this history entry?\');">Remove</a>';
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
    </div>

    <script>
        // Logout popup functionality
        document.addEventListener('DOMContentLoaded', function() {
            const logoutTrigger = document.getElementById('logoutTrigger');
            const logoutPopup = document.getElementById('logoutPopup');
            const cancelLogout = document.getElementById('cancelLogout');
            const confirmLogout = document.getElementById('confirmLogout');

            // Show popup when logout is clicked
            logoutTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                logoutPopup.classList.add('active');
            });

            // Hide popup when cancel is clicked
            cancelLogout.addEventListener('click', function() {
                logoutPopup.classList.remove('active');
            });

            // Hide popup when clicking outside the content
            logoutPopup.addEventListener('click', function(e) {
                if (e.target === logoutPopup) {
                    logoutPopup.classList.remove('active');
                }
            });

            // Optional: Close with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && logoutPopup.classList.contains('active')) {
                    logoutPopup.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>