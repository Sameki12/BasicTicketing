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

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Build the SQL query to search for site_id and wen_num columns
$query = "SELECT * FROM site_info 
          WHERE site_id LIKE '%$searchTerm%'";

if (!$results = $conn->query($query)){
    echo "Query failed: (" . $conn->errno . ") " . $conn->error;
}

// If no results were found with site_id, search for wen_num
if ($results->num_rows === 0) {
    $query = "SELECT * FROM site_info 
              WHERE wen_num LIKE '%$searchTerm%'";
    
    if (!$results = $conn->query($query)){
        echo "Query failed: (" . $conn->errno . ") " . $conn->error;
    }
}

$conn->close();
?>

<div class="center-content">
  <form action="" method="GET">
      <input type="text" id="search" name="search" placeholder="Search" value="">
      <button type="submit">Search</button>
  </form>
  <table>
      <tr>
          <th>Carlisle #</th>
          <th>Wendy's #</th>
          <th>Site Name</th>
          <th>Address</th>
          <th>City</th>
          <th>State</th>
          <th>Zip</th>
          <th>Phone</th>
          <th>DM</th>
          <th>DAO</th>
          <th>IT Tech</th>
          <!--<th>Maintenance Tech</th>-->
          <th>ISP</th>
          <th>PSP</th>
          <?php if ($_SESSION["role"] === "admin"): ?>
              <th>Edit</th>
          <?php endif; ?>
      </tr>
      <?php while($row = $results->fetch_assoc()){ ?>
      <tr>
          <td><?= $row['site_id']?></td>
          <td><?= $row['wen_num']?></td>
          <td><?= $row['site_name']?></td>
          <td><?= $row['address']?></td>
          <td><?= $row['city']?></td>
          <td><?= $row['st']?></td>
          <td><?= $row['zip']?></td>
          <td><?= $row['phone']?></td>
          <td><?= $row['dm']?></td>
          <td><?= $row['dao']?></td>
          <td><?= $row['it_tech']?></td>
          <td><?= $row['maint_tech']?></td>
          <td><?= $row['isp']?></td>
          <td><?= $row['psp']?></td>
          <?php if ($_SESSION["role"] === "admin"): ?>
              <td><a href="src/edit.php?site_id=<?= $row['site_id']?>">Edit</a></td>
          <?php endif; ?>
          <!--<td><a href="src/delete.php?site_id=<?= $row['site_id']?>">Delete</a></td>-->
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