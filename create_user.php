<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
<?php
include 'css/common_header.php';
?>

    <h2>Create New User</h2>
    
    <form method="post" action="src/user_creation_processing.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="role">Role:</label>
        <select name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
            <option value="technician">Technician</option>
        </select><br><br>
        
        <input type="submit" value="Create User">
    </form>
</body>
</html>