<!DOCTYPE html>
<html>
<head>
    <title>Edit User - Carlisle Ticket System</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>

<?php
include 'css/common_header.php';
include 'src/components/databaseConnection.php';
session_start(); // Start the PHP session

// Access and print session variables
//foreach ($_SESSION as $key => $value) {
//    echo "$key = $value <br>";
//}

// Check if the user is logged in as an admin
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    echo "You do not have permission to access this page.";
    exit;
}

// Retrieve the user_id from the query parameter
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

// Retrieve user details based on the user_id
$query = "SELECT * FROM users WHERE user_id = $user_id";
$result = $conn->query($query);

if (!$result) {
    echo "Error: " . $conn->error;
}

if ($result->num_rows === 0) {
    echo "User not found.";
    exit;
}

$row = $result->fetch_assoc();

// Initialize variables for the form inputs
$newUsername = $row['username'];
$newPassword = '';
$changePassword = false;
$newRole = $row['role'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = $_POST['new_username'];
    $newRole = $_POST['new_role'];
    $changePassword = isset($_POST['change_password']);
    
    // Check if the user wants to change the password
    if ($changePassword) {
        $newPassword = $_POST['new_password'];

        // Hash the new password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    } else {
        // If not changing the password, keep the existing password
        $hashedNewPassword = $row['password'];
    }

    // Update the user's username, password, and role in the database
    $updateQuery = "UPDATE users SET username = '$newUsername', role = '$newRole', password = '$hashedNewPassword' WHERE user_id = $user_id";

    if ($conn->query($updateQuery) === TRUE) {
        echo "User information updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<div class="center-content">
    <h2>Edit User</h2>
    <form method="post">
        <label for="new_username">New Username:</label>
        <input type="text" name="new_username" value="<?= $newUsername ?>" required><br><br>

        <label for="change_password">Change Password:</label>
        <input type="checkbox" name="change_password" id="change_password">
        <br><br>

        <div id="password_fields" style="display: none;">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password">
            <br><br>
        </div>

        <label for="new_role">New Role:</label>
        <select name="new_role">
            <option value="user" <?= ($newRole === 'user') ? 'selected' : '' ?>>User</option>
            <option value="technician" <?= ($newRole === 'technician') ? 'selected' : '' ?>>Technician</option>
            <option value="admin" <?= ($newRole === 'admin') ? 'selected' : '' ?>>Admin</option>
        </select><br><br>

        <input type="submit" value="Update User">
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const changePasswordCheckbox = document.getElementById('change_password');
        const passwordFields = document.getElementById('password_fields');

        changePasswordCheckbox.addEventListener('change', function() {
            passwordFields.style.display = changePasswordCheckbox.checked ? 'block' : 'none';
        });
    });
</script>

</body>
</html>