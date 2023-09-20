<!DOCTYPE html>
<html>
<head>
    <title>Add Site to Site Database</title>
</head>
<body>

<?php
include 'components/databaseConnection.php';
  
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    //Get data from form
    $site_id = $_POST["site_id"];
    $wen_num = $_POST["wen_num"];
    $site_name = $_POST["site_name"];

    //Create and exectue SQL Query
    $sql = "INSERT INTO site_info (site_id,wen_num,site_name) VALUES (?, ?, ?)";

    //Prepare statement
    $stmt = $conn->prepare($sql);
    //Bind the paremeters
    $stmt->bind_param("iss", $site_id,$wen_num,$site_name);
    //execute the statement
    if ($stmt->execute()) {
        echo "New site added successfully!" ;
    } else {
        echo "Error dude: " . $sql . "<br>" . $conn->error;
    }
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<h2>Add Site Record</h2>
<form method="post" action="">
    <label for="site_id">Carlisle #</label>
    <input type="text" name="site_id" required><br>

    <label for="wen_num">Wendy's #</label>
    <input type="text" name="wen_num" required><br>

    <label for="site_name">Site Name</label>
    <input type="text" name="site_name"><br>

    <input type="submit" value="Add Site">
</form>

<a href="../displaysites.php?site_id=<?= $row['site_id']?>">Back to Site List</a>
</body>
</html>