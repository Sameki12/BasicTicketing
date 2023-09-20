<?php
$servername = 'localhost';
$dbusername = 'aholton';
$dbpassword = 'Mountred23#';
$dbname = 'ticket_main';

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>