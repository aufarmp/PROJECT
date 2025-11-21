<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_genre'])) {
    $genre_name = mysqli_real_escape_string($conn, $_POST['genre_name']);
    if (!empty($genre_name)) {
        $sql = "INSERT INTO tb_genre (name) VALUES (?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $genre_name);
        mysqli_stmt_execute($stmt);
        header("Location: manage_genres.php?status=added");
        exit();
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $sql_junction = "DELETE FROM tb_komik_genre WHERE genre_id = ?";
    $stmt_junction = mysqli_prepare($conn, $sql_junction);
    mysqli_stmt_bind_param($stmt_junction, "i", $delete_id);
    mysqli_stmt_execute($stmt_junction);

    $sql_genre = "DELETE FROM tb_genre WHERE genre_id = ?";
    $stmt_genre = mysqli_prepare($conn, $sql_genre);
    mysqli_stmt_bind_param($stmt_genre, "i", $delete_id);
    mysqli_stmt_execute($stmt_genre);
    header("Location: manage_genres.php?status=deleted");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Genres</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
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

    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="dashboard.php" class="nav-button">Dashboard</a>
            <a href="add_comic.php" class="nav-button">Add New Comic</a>
            <a href="add_chapter.php" class="nav-button">Add New Chapter</a>
            <a href="manage_genres.php" class="nav-button active">Manage Genres</a>
            <a href="manage_users.php" class="nav-button">Manage Users</a>
            <a class="nav-button logout-button" id="logoutTrigger">Logout</a>
        </div>

        <div class="main-content">
            <h1>Manage Genres</h1>
            <p>Add, view, or delete comic genres from the system.</p>
            <hr>

            <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'added') echo '<div class="feedback success">Genre added successfully!</div>';
                if ($_GET['status'] == 'deleted') echo '<div class="feedback success">Genre deleted successfully.</div>';
            }
            ?>

            <div class="form-container" style="margin-bottom: 30px;">
                <h2>Add New Genre</h2>
                <form action="manage_genres.php" method="POST">
                    <div class="form-group">
                        <label for="genre_name">Genre Name:</label>
                        <input type="text" id="genre_name" name="genre_name" required>
                    </div>
                    <button type="submit" name="add_genre">Add Genre</button>
                </form>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Genre ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT genre_id, name FROM tb_genre ORDER BY genre_id ASC";
                        $result = mysqli_query($conn, $sql);
                        while ($genre = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo '<td>' . $genre['genre_id'] . '</td>';
                            echo '<td>' . htmlspecialchars($genre['name']) . '</td>';
                            echo '<td>
                                    <a href="manage_genres.php?delete_id=' . $genre['genre_id'] . '" class="action-btn delete-btn" onclick="return confirm(\'Are you sure you want to delete this genre?\');">Delete</a>
                                  </td>';
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
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