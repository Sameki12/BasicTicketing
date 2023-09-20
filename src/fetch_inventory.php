<?php
include 'components/databaseConnection.php';

$query = "SELECT equipment_id, name FROM inventory";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching inventory: " . $conn->error); // Notice of error

} else {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($data); // Return inventory items as JSON
}

$conn->close();
?>
