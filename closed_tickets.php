<!DOCTYPE html>
<html>
<head>
    <title>Closed Tickets</title>
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

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Modify the query to select closed tickets with a search filter
$query = "SELECT * FROM tickets WHERE status = 'Closed' AND (site_id LIKE '%$searchTerm%' OR issue_desc LIKE '%$searchTerm%')";
$results = $conn->query($query);

if (!$results) {
    echo "Error: " . $conn->error;
}
?>

<div class="center-content">
    <form action="" method="GET">
        <input type="text" id="search" name="search" placeholder="Search" value="<?= $searchTerm ?>">
        <button type="submit">Search</button>
    </form>

    <table>
        <tr>
            <th>Ticket #</th>
            <th>Site #</th>
            <th>Status</th>
            <th>Notes</th>
        </tr>
        <?php while($row = $results->fetch_assoc()){ ?>
        <tr>
            <td><?= $row['id']?></td>
            <td><?= $row['site_id']?></td>
            <td><?= $row['status']?></td>
            <td><?= $row['issue_desc']?></td>
        </tr> 
        <?php }?>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const searchForm = document.querySelector('form');
        searchForm.addEventListener('submit', function(event) {
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