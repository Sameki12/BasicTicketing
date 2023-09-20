<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'components/databaseConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user data from the form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
   
    // Perform data validation and hashing for the password (including salting)
    $password = $_POST['password']; // Get the user's inputted password
    $salt = bin2hex(random_bytes(16)); // Generate a 16-byte random salt
    $hashedPassword = password_hash($password . $salt, PASSWORD_DEFAULT);

    // Insert user data into the database
    $query = "INSERT INTO users (username, email, password, salt, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        echo "Prepare failed: " . $conn->error;
        exit();
    }

    $stmt->bind_param("sssss", $username, $email, $hashedPassword, $salt, $role);

    if ($stmt->execute()) {
        // User creation successful, you can redirect or display a success message
        header("Location: ../admin_interface.php");
        exit();
    } else {
        // User creation failed, handle the error (e.g., display an error message)
        echo "Error: " . $stmt->error;
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
