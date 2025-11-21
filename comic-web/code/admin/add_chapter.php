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
            <a href="add_chapter.php" class="nav-button active">Add New Chapter</a>
            <a href="manage_genres.php" class="nav-button">Manage Genres</a> 
            <a href="manage_users.php" class="nav-button">Manage Users</a>
            <a class="nav-button logout-button" id="logoutTrigger">Logout</a>
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