<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'components/databaseConnection.php';

// Retrieve form data
$priority = $_POST['priority'];
$tech_assigned = $_POST['tech_assigned'];
$category = $_POST['category'];
$issue_desc = $_POST['issue_desc'];
$site_id = $_POST['site_id'];

//testing
//var_dump($priority);
//var_dump($tech_assigned);
//var_dump($category);
//var_dump($issue_desc);

// Prepare and execute SQL query
$status = "Open";
$date_submit = date("Y-m-d");
	//echo $priority . $tech_assigned;
$query = "INSERT INTO tickets (site_id, status, date_submit, priority, tech_assigned, issue_desc)
          VALUES ($site_id, '$status', '$date_submit', '$priority', '$tech_assigned', '$issue_desc')";

if ($conn->query($query) === TRUE) {
    echo "Ticket submitted successfully!";
    header("refresh:1;url=../tickets.php"); // Redirect after 2 seconds
    exit; // Exit to prevent further output
} else {
    echo "Error: " . $query . "<br>" . $conn->error;
}

$conn->close();
?>