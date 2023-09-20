<!DOCTYPE html>
<html>
<head>
    <title>Delete Site from Site Database</title>
	<link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>

<?php
include 'components/databaseConnection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_id"])) {
    $delete_id = $_POST["delete_id"];

    // Create and execute SQL query to delete a site
    $sql = "DELETE FROM site_info WHERE site_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "Site deleted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt->close();
}

// Retrieve all rows from the site_info table
$sql = "SELECT * FROM site_info";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>All Sites</h2>";
    echo "<table border='1'><tr><th>Carlisle #</th><th>Wendy's #</th><th>Site Name</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["site_id"] . "</td><td>" . $row["wen_num"] . "</td><td>" . $row["site_name"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No sites found.";
}

// Close the connection
$conn->close();
?>

<h2>Delete Site</h2>
<form method="post" action="">
    <label for="delete_id">Enter Carlisle # to Delete:</label>
    <input type="text" name="delete_id" required><br>

    <input type="submit" value="Delete Site">
</form>

<a href="../displaysites.php?site_id=<?= $row['site_id']?>">Back to Site List</a>
</body>
</html>