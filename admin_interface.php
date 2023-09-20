<!DOCTYPE html>
<html>
<head>
    <title>Admin Interface</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
 
<?php
session_start(); // Start the PHP session
include 'css/common_header.php';
include 'src/components/databaseConnection.php';

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Build the SQL query to search for username
$query = "SELECT * FROM users 
          WHERE username LIKE '%$searchTerm%'";

if (!$results = $conn->query($query)){
    echo "Query failed: (" . $conn->errno . ") " . $conn->error;
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
          <th>User ID</th>
          <th>Username</th>
          <th>Email Address</th>
          <th>Role</th>
          <th>User Created On</th>

      </tr>
      <?php 
      if ($results) {
          while($row = $results->fetch_assoc()){ 
      ?>
      <tr>
          <td><?= $row['user_id']?></td>
          <td><?= $row['username']?></td>
          <td><?= $row['email']?></td>
          <td><?= $row['role']?></td>
          <td><?= $row['created_at']?></td>
          <td><a href="edit_user.php?user_id=<?= $row['user_id']?>">Edit</a></td>

      </tr> 
      <?php 
          } 
      } else {
          echo "No results found.";
      }
      ?>
  </table>
  <button onclick="location.href='create_user.php';">Create User</button>
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
