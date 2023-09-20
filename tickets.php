<!DOCTYPE html>
<html>
<head>
    <title>Carlisle Ticket System</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>

<?php
include 'css/common_header.php';
include 'src/components/databaseConnection.php';
session_start(); // Start the PHP session

// Access and print session variables
//foreach ($_SESSION as $key => $value) {
//    echo "$key = $value <br>";
//}

// Retrieve the selected limit from the query parameter
$selectedLimit = isset($_GET['limit']) ? $_GET['limit'] : 10;

// Determine the user's role (assuming it's stored in the session)
$userRole = $_SESSION["role"];

// Modify the SQL query based on the user's role
if ($userRole === "admin") {
    $query = "SELECT * FROM tickets WHERE status IN ('Open', 'Waiting') LIMIT $selectedLimit";
} else if ($userRole === "technician") {
    // Assuming the technician's username is stored in the session
    $technicianUsername = $_SESSION["username"];
    $query = "SELECT * FROM tickets WHERE tech_assigned = '$technicianUsername' AND status IN ('Open', 'Waiting') LIMIT $selectedLimit";
} else {
    // Handle other user roles as needed
}

$results = $conn->query($query);

if (!$results) {
    echo "Error: " . $conn->error;
}
?>

<div class="center-content">
    <table>
        <tr>
            <th>Ticket #</th>
            <th>Site #</th>
            <th>Tech Assigned</th>
            <th>Status</th>
            <th>Notes</th>
        </tr>
        <?php while($row = $results->fetch_assoc()){ ?>
        <tr>
            <td><a href="edit_ticket.php?ticket_id=<?= $row['id']?>"><?= $row['id']?></a></td>
            <td><a href="edit_ticket.php?ticket_id=<?= $row['id']?>"><?= $row['site_id']?></a></td>
            <td><a href="edit_ticket.php?ticket_id=<?= $row['id']?>"><?= $row['tech_assigned']?></a></td>
            <td><a href="edit_ticket.php?ticket_id=<?= $row['id']?>"><?= $row['status']?></a></td>
            <td><a href="edit_ticket.php?ticket_id=<?= $row['id']?>"><?= $row['issue_desc']?></a></td>
        </tr> 
        <?php }?>
    </table>
    <label for="limit">Show:</label>
<select id="limit" name="limit">
    <option value="10" <?= $selectedLimit == 10 ? 'selected' : '' ?>>10</option>
    <option value="50" <?= $selectedLimit == 50 ? 'selected' : '' ?>>50</option>
    <option value="100" <?= $selectedLimit == 100 ? 'selected' : '' ?>>100</option>
</select>

    <button id="applyLimit">Apply</button>
    <script>
        document.getElementById("applyLimit").addEventListener("click", function() {
            var selectedLimit = document.getElementById("limit").value;
            window.location.href = window.location.pathname + "?limit=" + selectedLimit;
        });
    </script>
</div>
</body>
</html>