<?php
include 'css/common_header.php';
include 'src/components/databaseConnection.php';
session_start(); // Start the PHP session

// Retrieve the ticket_id from the query parameter
$ticket_id = $_GET['ticket_id'];

// Retrieve the ticket details based on the ticket_id
$query = "SELECT * FROM tickets WHERE id = $ticket_id";
$result = $conn->query($query);

if (!$result) {
    echo "Error: " . $conn->error;
}

if ($result->num_rows === 0) {
    echo "Ticket not found.";
    exit;
}

$row = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newStatus = $_POST['status'];
    $newPriority = $_POST['priority'];
    $newTechAssigned = $_POST['tech_assigned'];
    $newIssueDesc = $_POST['issue_desc'];

    // Update the ticket details in the database
    $updateQuery = "UPDATE tickets SET status = '$newStatus', priority = '$newPriority', tech_assigned = '$newTechAssigned', issue_desc = '$newIssueDesc'";

    if ($newStatus === 'Closed') {
        $dateClosed = date("Y-m-d H:i:s");
        $updateQuery .= ", date_closed = '$dateClosed'";
    }

    $updateQuery .= " WHERE id = $ticket_id";

    if ($conn->query($updateQuery) === TRUE) {
        echo "Ticket updated successfully!";
        if ($newStatus === 'Closed') {
            echo " Date closed: $dateClosed";
        }

        // Check if the "Inventory Used" checkbox is selected
        if (isset($_POST['used_inventory_checkbox'])) {
            $equipmentId = $_POST['equipment_id']; // Assuming you have an equipment_id
            $quantityUsed = $_POST['quantity'];
            $techUsed = $_SESSION['user_id']; // Assuming you store the user_id in the session
            $siteUsed = $row['site_id'];

            // Insert the usage record into the item_usage table
            $insertUsageQuery = "INSERT INTO item_usage (equipment_id, quantity_used, tech_used, site_used) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertUsageQuery);
            $stmt->bind_param("iisi", $equipmentId, $quantityUsed, $techUsed, $siteUsed);

            if ($stmt->execute()) {
                echo "Inventory usage record added successfully!";
            } else {
                echo "Error adding inventory usage record: " . $conn->error;
            }
        }

        // Redirect to tickets.php
        header("Location: tickets.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>