<!DOCTYPE html>
<html>
<head>
    <title>Edit Ticket</title>
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

    if ($conn->query($updateQuery) === TRUE) {
        echo "Ticket updated successfully!";
        if ($newStatus === 'Closed') {
            echo " Date closed: $dateClosed";
        }
        
        // Redirect to tickets.php
        header("Location: tickets.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
    
}
?>

<div class="center-content">
    <h2>Edit Ticket #<?= $row['id'] ?></h2>
    <form method="post">
        <label for="status">Status:</label>
        <select name="status">
            <option value="Open" <?= ($row['status'] === 'Open') ? 'selected' : '' ?>>Open</option>
            <option value="Waiting" <?= ($row['status'] === 'Waiting') ? 'selected' : '' ?>>Waiting</option>
            <option value="Closed" <?= ($row['status'] === 'Closed') ? 'selected' : '' ?>>Closed</option>
        </select><br><br>

        <label for="priority">Priority:</label>
        <select name="priority">
            <option value="Low" <?= ($row['priority'] === 'Low') ? 'selected' : '' ?>>Low</option>
            <option value="Med" <?= ($row['priority'] === 'Med') ? 'selected' : '' ?>>Medium</option>
            <option value="High" <?= ($row['priority'] === 'High') ? 'selected' : '' ?>>High</option>
        </select><br><br>

        <label for="tech_assigned">Tech Assigned:</label>
        <select name="tech_assigned">
        <?php
            // Retrieve the list of usernames from the "users" table for technicians
            $query = "SELECT username FROM users WHERE role = 'technician'";
            $techResults = $conn->query($query);

            if ($techResults) {
                while ($techRow = $techResults->fetch_assoc()) {
                    $selected = ($row['tech_assigned'] === $techRow['username']) ? 'selected' : '';
                    echo '<option value="' . $techRow['username'] . '" ' . $selected . '>' . $techRow['username'] . '</option>';
                }
            }
            ?>
        </select><br><br>

        <label for="issue_desc">Issue Description:</label><br>
        <textarea name="issue_desc" rows="4" cols="50"><?= $row['issue_desc'] ?></textarea><br><br>
        
        <label for="used_inventory">Inventory Used:</label>
        <input type="checkbox" name="used_inventory" id="used_inventory_checkbox" value="1" <?= ($row['used_inventory'] == 1) ? 'checked' : '' ?>>
        <br><br>

        <!-- Hidden dropdown initially -->
        <div id="inventory_dropdown" style="display: none;">
            <label for="selected_inventory">Select Inventory Item:</label>
            <select name="selected_inventory" id="selected_inventory">
                <!-- Options will be populated dynamically using JavaScript -->
            </select>
                    <!-- Hidden Quantity input box -->
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="1"><br><br>
        </div>

        <label for="order_item">Item Needs to Be Ordered:</label>
        <input type="checkbox" name="order_item" id="order_item_checkbox" value="1" <?= ($row['order_item'] == 1) ? 'checked' : '' ?>>
        <br><br>

        <input type="submit" value="Update Ticket">
    </form>
</div>

<!-- Script to show/hide the quantity box when inventory used is checked -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const usedInventoryCheckbox = document.getElementById('used_inventory_checkbox');
        const quantityInput = document.getElementById('quantity');

        // Function to show/hide the quantity input box
        function toggleQuantityInput() {
            quantityInput.style.display = usedInventoryCheckbox.checked ? 'block' : 'none';
        }

        // Toggle the visibility of the quantity input box when the checkbox changes
        usedInventoryCheckbox.addEventListener('change', function () {
            toggleQuantityInput();
        });

        // Initially set the state of the quantity input box based on the checkbox
        toggleQuantityInput();
    });
</script>

<!-- Script to show dynamic inventory dropdown box when the box is checked -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const usedInventoryCheckbox = document.getElementById('used_inventory_checkbox');
        const inventoryDropdown = document.getElementById('inventory_dropdown');
        const selectedInventoryDropdown = document.getElementById('selected_inventory');

        // Function to fetch inventory items from the server
        function fetchInventoryItems() {
            fetch('src/fetch_inventory.php') // Replace with the correct URL to fetch items from your server
                .then(response => response.json())
                .then(data => {
                    // Clear existing options
                    selectedInventoryDropdown.innerHTML = '';

                    // Populate the dropdown with items from the server response
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.equipment_id; // Replace with the correct item ID
                        option.textContent = item.name;
                        selectedInventoryDropdown.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching inventory:', error));
        }

        // Toggle the visibility of the dropdown when the checkbox changes
        usedInventoryCheckbox.addEventListener('change', function () {
            if (usedInventoryCheckbox.checked) {
                inventoryDropdown.style.display = 'block';
                fetchInventoryItems(); // Fetch items when the checkbox is checked
            } else {
                inventoryDropdown.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>