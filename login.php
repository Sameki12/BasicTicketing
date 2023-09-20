<!DOCTYPE html>
<html>
<head>
    <title>Login to Ticketing System</title>
</head>
<body>

<?php
// Include the database connection file
include 'src/components/databaseConnection.php';
 
// Start a session
session_start(); // Start the PHP session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Validate user credentials against the database
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            // Password is correct; set user session
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] === "technician") {
                header("Location: timeclock.php"); // Redirect tech users to timeclock.php
            } else {
                header("Location: tickets.php"); // Redirect other users to the tickets.php
            }
            exit();
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Login">
    </form>
    
    <?php
    if (isset($error)) {
        echo "<p>Error: $error</p>";
    }
    ?>
</body>
</html>