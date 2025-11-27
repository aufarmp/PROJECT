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
    <title>Manage Users</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="dashboard.php" class="nav-button">Dashboard</a>
            <a href="add_comic.php" class="nav-button">Add New Comic</a>
            <a href="add_chapter.php" class="nav-button">Add New Chapter</a>
            <a href="manage_genres.php" class="nav-button">Manage Genres</a> 
            <a href="manage_users.php" class="nav-button active">Manage Users</a>
            <a href="#" class="nav-button logout-button" onclick="showLogoutModal(event)">Logout</a>
        </div>

        <div class="main-content">
            <h1>Manage Users</h1>
            <p>Here is a list of all registered users in the system.</p>
            <hr>

            <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'user_updated') {
                    echo '<div class="feedback success">User details updated successfully!</div>';
                } elseif ($_GET['status'] == 'user_deleted') {
                    echo '<div class="feedback success">User deleted successfully.</div>';
                } elseif ($_GET['status'] == 'delete_self_error') {
                    echo '<div class="feedback error">You cannot delete your own admin account.</div>';
                }
            }
            ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT user_id, username, email, role FROM tb_user";
                        $result = mysqli_query($conn, $sql);
                        while ($user = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo '<td>' . $user['user_id'] . '</td>';
                            echo '<td>' . htmlspecialchars($user['username']) . '</td>';
                            echo '<td>' . htmlspecialchars($user['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($user['role']) . '</td>';
                            echo '<td>
                                    <a href="edit_user.php?id=' . $user['user_id'] . '" class="action-btn edit-btn">Edit</a>
                                    <a href="#" class="action-btn delete-btn" onclick="showDeleteModal(' . $user['user_id'] . ', \'' . htmlspecialchars(addslashes($user['username'])) . '\', \'user\')">Delete</a>
                                  </td>';
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="delete-modal-overlay">
            <div class="delete-modal-content">
                <h3>Confirm Delete</h3>
                <p id="deleteMessage">Are you sure you want to delete this item?</p>
                <div class="delete-modal-buttons">
                    <a href="#" id="confirmDelete" class="delete-modal-btn confirm">Yes, Delete</a>
                    <button class="delete-modal-btn cancel" onclick="closeDeleteModal()">Cancel</button>
                </div>
            </div>
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
        function showDeleteModal(id, name, type) {
            event.preventDefault();
            
            let message = '';
            let deleteUrl = '';
            
            if (type === 'comic') {
                message = `Are you sure you want to delete the comic "${name}"? This will also delete ALL its chapters and pages. This action cannot be undone.`;
                deleteUrl = `delete_comic.php?id=${id}`;
            } else if (type === 'genre') {
                message = `Are you sure you want to delete the genre "${name}"?`;
                deleteUrl = `manage_genres.php?delete_id=${id}`;
            } else if (type === 'user') {
                message = `Are you sure you want to delete the user "${name}"?`;
                deleteUrl = `delete_user.php?id=${id}`;
            }
            
            document.getElementById('deleteMessage').textContent = message;
            document.getElementById('confirmDelete').href = deleteUrl;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        function showLogoutModal(event) {
            event.preventDefault();
            document.getElementById('logoutModal').style.display = 'flex';
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        // Close modals when clicking outside the modal content
        document.getElementById('deleteModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeDeleteModal();
            }
        });

        document.getElementById('logoutModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeLogoutModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
                closeLogoutModal();
            }
        });
        </script>
    </div>
</body>
</html>