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
            <a href="../logout.php" class="nav-button logout-button" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
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
                                    <a href="#" class="action-btn delete-btn" onclick="showDeleteUserModal(' . $user['user_id'] . ', \'' . htmlspecialchars(addslashes($user['username'])) . '\', ' . ($user['user_id'] == $_SESSION['user_id'] ? 'true' : 'false') . '); return false;">Delete</a>
                                  </td>';
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete User Confirmation Modal -->
    <div class="modal-overlay" id="deleteUserModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Delete User</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the user "<span id="userName"></span>"?</p>
                <div class="modal-body warning" id="selfDeleteWarning" style="display: none;">
                    <strong>Warning:</strong> You cannot delete your own admin account while logged in!
                </div>
                <div class="modal-body warning" id="normalDeleteWarning">
                    <strong>Note:</strong> This will permanently remove the user and all their data (bookmarks, reading history, etc.).
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn cancel" onclick="hideDeleteUserModal()">Cancel</button>
                <a href="#" class="modal-btn confirm" id="confirmDeleteUserBtn">Delete User</a>
            </div>
        </div>
    </div>

    <script>
        function showDeleteUserModal(userId, userName, selfDelete) {
            document.getElementById('userName').textContent = userName;
            
            if (selfDelete) {
                document.getElementById('selfDeleteWarning').style.display = 'block';
                document.getElementById('normalDeleteWarning').style.display = 'none';
                document.getElementById('confirmDeleteUserBtn').style.display = 'none';
            } else {
                document.getElementById('selfDeleteWarning').style.display = 'none';
                document.getElementById('normalDeleteWarning').style.display = 'block';
                document.getElementById('confirmDeleteUserBtn').style.display = 'block';
                document.getElementById('confirmDeleteUserBtn').href = 'delete_user.php?id=' + userId;
            }
            
            document.getElementById('deleteUserModal').classList.add('active');
        }

        function hideDeleteUserModal() {
            document.getElementById('deleteUserModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('deleteUserModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideDeleteUserModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideDeleteUserModal();
            }
        });
    </script>
</body>
</html>