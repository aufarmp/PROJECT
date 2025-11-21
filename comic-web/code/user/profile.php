<?php
session_start();
include '../connection.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$sql_user = "SELECT username, email, profile_picture_url FROM tb_user WHERE user_id = ?";
$stmt_user = mysqli_prepare($conn, $sql_user);
mysqli_stmt_bind_param($stmt_user, "i", $user_id);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);
$user_data = mysqli_fetch_assoc($result_user);

$profile_pic = !empty($user_data['profile_picture_url']) ? $user_data['profile_picture_url'] : '/comic_project/comic-web/assets/profile_pictures/default.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Comic Web</title>
    <link rel="stylesheet" href="../../assets/css/user_style.css">

    <style>
        .profile-container { max-width: 800px; margin: 20px auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
        .profile-picture { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; display: block; margin: 0 auto 20px auto; border: 3px solid #eee; }
        .form-section { border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px; }
        .form-section h3 { margin-top: 0; color: #2c3e50; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .profile-container button { padding: 10px 15px; font-size: 14px; font-weight: bold; background-color: #1abc9c; color: white; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s; }
        .profile-container button:hover { background-color: #16a085; }

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
            <a href="profile.php" class="nav-button active">Profile</a>
            <a class="nav-button logout-button" id="logoutTrigger">Logout</a>
        </div>

        <div class="main-content">
            <h2 class="page-heading">My Profile</h2>

             <?php 
             if (isset($_GET['status'])) {
                if ($_GET['status'] == 'success') echo '<div class="feedback success" style="max-width: 800px; margin: 0 auto 20px auto;">Profile updated successfully!</div>';
                if ($_GET['status'] == 'error_type') echo '<div class="feedback error" style="max-width: 800px; margin: 0 auto 20px auto;">Error: Invalid file type for profile picture.</div>';
                if ($_GET['status'] == 'error_size') echo '<div class="feedback error" style="max-width: 800px; margin: 0 auto 20px auto;">Error: Profile picture file is too large.</div>';
                if ($_GET['status'] == 'error_upload') echo '<div class="feedback error" style="max-width: 800px; margin: 0 auto 20px auto;">Error uploading profile picture.</div>';
                if ($_GET['status'] == 'error_duplicate') echo '<div class="feedback error" style="max-width: 800px; margin: 0 auto 20px auto;">Error: Username or email already exists.</div>';
                if ($_GET['status'] == 'error_db') echo '<div class="feedback error" style="max-width: 800px; margin: 0 auto 20px auto;">Database error. Please try again.</div>';
            }
            ?> 

            <div class="profile-container">
                <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="profile-picture">

                <form action="update_profile.php" method="POST" enctype="multipart/form-data" class="form-section">
                    <h3>Update Profile Picture</h3>
                    <div class="form-group">
                        <label for="profile_pic_upload">Select Image:</label>
                        <input type="file" id="profile_pic_upload" name="profile_picture" accept="image/jpeg, image/png, image/gif">
                    </div>
                    <button type="submit" name="update_picture">Upload Picture</button>
                </form>

                <form action="update_profile.php" method="POST" class="form-section">
                    <h3>Update Account Details</h3>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                    </div>
                    <button type="submit" name="update_details">Update Details</button>
                </form>

                <form action="update_profile.php" method="POST" class="form-section">
                    <h3>Change Password</h3>
                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="update_password">Change Password</button>
                </form>
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