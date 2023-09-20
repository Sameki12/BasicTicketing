<!DOCTYPE html>
<html>
<head>
    <title>Inventory Manager</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
 
<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include 'css/common_header.php';
include 'src/components/databaseConnection.php';
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    echo "You do not have permission to access this page.";
    exit;
}

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Handle form submission for adding items
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $cost = $_POST['cost'];

    // Insert the new item into the inventory table
    $insertQuery = "INSERT INTO inventory (name, description, cost) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssd", $name, $description, $cost);

    if ($stmt->execute()) {
        echo "Item added to the inventory successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Build the SQL query to search for name column
$query = "SELECT * FROM inventory 
          WHERE name LIKE '%$searchTerm%'";

if (!$results = $conn->query($query)) {
    echo "Query failed: (" . $conn->errno . ") " . $conn->error;
}
?>

<div class="center-content">
    <form action="" method="GET">
        <input type="text" id="search" name="search" placeholder="Search" value="">
        <button type="submit">Search</button>
    </form>

    <!-- Add item form -->
    <h2>Add Item</h2>
    <form action="" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="4" cols="50" required></textarea><br><br>

        <label for="cost">Cost:</label>
        <input type="number" id="cost" name="cost" required><br><br>

        <input type="submit" value="Add Item">
    </form>

    <!-- Inventory table -->
    <table>
        <tr>
            <th>Equipment Id</th>
            <th>Name</th>
            <th>Description</th>
            <th>Cost</th>
            <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                <th>Edit</th>
            <?php endif; ?>
        </tr>
        <?php while ($row = $results->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['equipment_id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['description'] ?></td>
                <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                    <td><a href="src/edit_inv.php?equipment_id=<?= $row['equipment_id'] ?>">Edit</a></td>
                <?php endif; ?>
            </tr>
        <?php } ?>
    </table>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search');
        const searchForm = document.querySelector('form');
        searchForm.addEventListener('submit', function (event) {
            // Prevent the default form submission
            event.preventDefault();

            const searchTerm = searchInput.value.trim();
            if (searchTerm === '') {
                // Redirect to the current page (clears the search query)
                window.location.href = window.location.pathname;
            } else {
                // Submit the form with the search term
                searchForm.submit();
            }
        });
    });
</script>
</body>
</html>