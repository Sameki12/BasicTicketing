<?php
$servername = 'localhost';
$dbusername = 'dbeditor';
$dbpassword = 'Mountred23#';
$dbname = 'usersdb';

// Create connection
$usersdb_conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($usersdb_conn->connect_error) {
    die('Connection failed: ' . $usersdb_conn->connect_error);
}
?>