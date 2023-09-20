<?php
// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION["user_id"])) {
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();
}

// Redirect to the login page after a delay
echo '<script>
    setTimeout(function() {
        window.location.href = "../login.php";
    }, 1000); // 1000 milliseconds (1 second) delay
    </script>';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
</head>
<body>
    <h2>Logout</h2>
    <!-- Display any additional HTML content if needed -->
    <p>You have been logged out. Redirecting to the login page...</p>
</body>
</html>
